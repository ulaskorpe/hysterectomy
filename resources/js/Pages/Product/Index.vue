<template>
    <Head head-key="title" :title="`${props.product_type.name} listesi`" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            
            <PageTitleWithLanguage :routeName="'products.index'" :title="`${props.product_type.name}`"/>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content  flex-column-fluid">

        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <div class="card">

                <div class="card-header px-4">
                    <div class="card-toolbar w-100 justify-content-between gap-2">
                        <Link :href="route('products.create',{productType:props.product_type.id,language:$page.props.current_language})" class="btn fw-bold btn-sm btn-success d-flex align-items-center">
                            <i class="bi bi-plus-circle fs-4"></i>
                            <span class="ms-1 lh-1">YENİ</span>
                        </Link>
                        <div class="hstack gap-2">
                            <Link :href="route('products.reorder',{productType:props.product_type.id,language:$page.props.current_language})" class="btn fw-bold btn-sm btn-light d-flex align-items-center">
                                <i class="bi bi-sort-down fs-4"></i>
                                <span class="ms-1 lh-1">SIRALA</span>
                            </Link>
                        </div>
                    </div>
                </div>

                <div class="card-header px-4" v-if="selectedItems.length === 0">
                    <Filter :filters="props.filters" :uri="route('products.index')" :extra-params="`productType=${props.product_type.id}`"/>
                </div>
                <div class="card-header px-4" v-if="selectedItems.length > 0">
                    <h5 class="card-title fs-6"><i class="bi bi-check2-circle me-2 fs-2"></i>{{ selectedItems.length }}</h5>
                    <div class="card-toolbar">
                        <DeleteBulkPrompt :uri="route('products.bulk.delete',{productType:props.product_type.id})" :items="selectedItems" :delete-done="resetSelectedItems"/>
                    </div>
                </div>
                <div class="card-body p-0">

                    <DataTable v-model:selection="selectedItems" :value="props.products.data" stripedRows dataKey="id" tableStyle="min-width: 50rem">
                        <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                        <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
                        <Column field="id" header="ID" headerStyle="width: 4rem"></Column>
                        <Column field="order_column" header="Sıra" headerStyle="width: 4rem"></Column>
                        <Column header="Dil" v-if="$page.props.languages.active.length > 1" headerStyle="width: 4rem">
                            <template #body="{ data }">
                                <div class="d-flex align-items-center">
                                    <img :src="`/dmn/media/flags/${data.language}.svg`" class="me-2" style="width: 14px" />
                                    <span class="text-uppercase">{{ data.language }}</span>
                                </div>
                            </template>
                        </Column>
                        <Column header="Bağlı Diller" v-if="$page.props.languages.active.length > 1">
                            <template #body="{ data }">
                                <div class="hstack gap-2">
                                    <div v-for="(lang,key) in $page.props.languages.active.filter((l) => l != data.language)" :key="key">
                                        <Link v-if="!hasConnectedLanguage(data,lang)" :href="route('products.create',{productType:props.product_type.id, language:lang, uuid:data.uuid, from_id:data.id})" class="btn fw-bold btn-sm btn-icon btn-light-danger text-uppercase" v-tooltip.top="'Oluştur'">{{ lang }}</Link>
                                        <Link v-if="hasConnectedLanguage(data,lang)" :href="route('products.edit',[hasConnectedLanguage(data,lang).id,{language:lang}])" class="btn fw-bold btn-sm btn-icon btn-light-info text-uppercase" v-tooltip.top="'Güncelle'">{{ lang }}</Link>
                                    </div>
                                </div>
                            </template>
                        </Column>
                        <Column header="İsim">
                            <template #body="{ data }">
                                <div class="hstack gap-2 align-items-center">
                                    <img :src="data.main_image[0].preview_url" width="50" v-if="data.main_image[0]" class="rounded"/>
                                    <i class="bi bi-star-fill text-primary" v-if="data.featured"></i>
                                    <span v-html="data.name"></span>
                                </div>
                            </template>
                        </Column>
                        <Column field="order_count" header="Satış Adet"></Column>
                        <Column class="text-end">
                            <template #body="{ data }">
                                <div class="d-flex flex-nowrap justify-content-end">
                                    <a :href="`${data.uri.final_uri}`" target="_blank" class="ms-2 btn btn-sm text-success btn-link">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <Link :href="route('products.create',{productType:props.product_type.id, language:data.language, copy_id:data.id})" v-tooltip.top="'Kopyala'">
                                        <Button icon="bi bi-copy" text rounded severity="info" class="ms-2"/>
                                    </Link>
                                    <Link :href="route('products.edit',[data,{language:data.language}])">
                                        <Button icon="bi bi-pencil" text rounded severity="info" class="ms-2"/>
                                    </Link>
                                    <Link :href="route('product-variants.index',data.id)" v-tooltip.top="'Varyasyonlar'" v-if="data.use_option_group">
                                        <Button icon="bi bi-list-nested" text rounded severity="dark" class="ms-2"/>
                                    </Link>
                                    <DeletePrompt :delete-done="resetSelectedItems" :uri="route('products.delete',[data.id,{language:$page.props.current_language, productType:props.product_type.id}])" :model="data"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                </div>
                <div class="card-footer">
                    <Pagination :links="props.products.links" :only="['products','flash']"/>
                </div>
            </div>

        </div>

    </div>

</template>

<script setup>

import {ref} from "vue";
import Pagination from "../../Components/Pagination";
import DeletePrompt from "../../Components/DeletePrompt";
import DeleteBulkPrompt from "../../Components/DeleteBulkPrompt";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import Filter from "../../Components/Filter";
import Button from "primevue/button";
import PageTitleWithLanguage from "../../Components/PageTitleWithLanguage";

const props = defineProps({
    products:Object,
    product_type:Object,
    filters:Object,
    sortables:{
        type:Array,
        default:[]
    }
});

const selectedItems = ref([]);
const metaKey = ref(true);
const selectedProduct = ref(null);

const resetSelectedItems = () => {
    selectedItems.value = [];
}

const hasConnectedLanguage = (content,lang) => {

    let data = null;
    const dfilter = content.connected_products.filter((x) => x.language == lang);

    if( dfilter.length ){
        data = dfilter[0];
    }
    return data;

}

const formatDateToMask = (date) => {
    var originalDate = new Date(date);
    var day = originalDate.getDate().toString().padStart(2, '0');
    var month = (originalDate.getMonth() + 1).toString().padStart(2, '0'); // Ay 0-11 arasında olduğu için +1 ekliyoruz.
    var year = originalDate.getFullYear();

    return day + '.' + month + '.' + year;
}

</script>