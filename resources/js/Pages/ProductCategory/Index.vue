<template>
    <Head head-key="title" title="Ürün Kategorileri" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            
            <PageTitleWithLanguage :routeName="'product-categories.index'" :title="`${props.product_type.name} Kategori`"/>

        </div>
    </div>
	
    
    <div id="kt_app_content" class="app-content  flex-column-fluid">

        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <div class="card">

                <div class="card-header px-4">
                    <div class="card-toolbar w-100 justify-content-between gap-2">
                        <NewProductCategory :selected-product-type-id="props.product_type.id" :language="$page.props.current_language"/>
                    </div>
                </div>

                <div class="card-header px-4" v-if="selectedItems.length > 0">
                    <h5 class="card-title fs-6"><i class="bi bi-check2-circle me-2 fs-2"></i>{{ selectedItems.length }}</h5>
                    <div class="card-toolbar">
                        <DeleteBulkPrompt :uri="route('product-categories.bulk.delete')" :items="selectedItems" :delete-done="resetSelectedItems"/>
                    </div>
                </div>

                <div class="card-body p-0">

                    <DataTable :value="props.categories" stripedRows dataKey="id" tableStyle="min-width: 50rem">
                        <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                        <Column field="id" header="ID" headerStyle="width: 4rem"></Column>
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
                                        <div v-if="!productTypeHasLanguage(lang)">
                                            <button type="button" class="btn fw-bold btn-sm btn-icon btn-light text-uppercase disabled" v-tooltip.top="'Bu dil için ürün tipi oluşturulmamış'" disabled>{{ lang }}</button>
                                        </div>
                                        <div v-else>
                                            <NewProductCategory :button-class="'btn fw-bold btn-sm btn-icon btn-light-danger text-uppercase'" :button-text="lang" :selected-product-type-id="productTypeHasLanguage(lang).id" :language="lang" :from-category="data" v-if="!hasConnectedLanguage(data,lang)"  v-tooltip.top="'Oluştur'"/>
                                            <button type="button" v-if="hasConnectedLanguage(data,lang)" @click="selectedCategory = hasConnectedLanguage(data,lang)" class="btn fw-bold btn-sm btn-icon btn-light-info text-uppercase" v-tooltip.top="'Güncelle'">{{ lang }}</button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Column>
                        <Column header="İsim">
                            <template #body="{ data }">
                                <span v-html="data.depth_name" :class="{'fw-bold fs-5':data.depth === 0}"></span>
                            </template>
                        </Column>
                        <Column field="slug" header="Slug"></Column>
                        <Column class="text-end">
                            <template #body="{ data }">
                                <div class="d-flex flex-nowrap justify-content-end">
                                    <a :href="`${data.uri.final_uri}`" target="_blank" class="ms-2 btn btn-sm text-success btn-link">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <Button icon="bi bi-pencil text-primary" text rounded severity="info" @click="selectedCategory = data"/>
                                    <DeletePrompt :delete-done="resetSelectedItems" :uri="route('product-categories.delete',[data.id,{language:$page.props.current_language,productType:props.product_type.id}])" :model="data"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                </div>
            </div>

        </div>

    </div>

    <EditProductCategory :selected-category="selectedCategory" :on-update-done="resetSelectedCategory" v-if="selectedCategory" />

</template>

<script setup>

import {ref} from "vue";

import PageTitleWithLanguage from "../../Components/PageTitleWithLanguage";

import NewProductCategory from "./NewProductCategory";
import EditProductCategory from "./EditProductCategory";
import DeletePrompt from "../../Components/DeletePrompt";
import DeleteBulkPrompt from "../../Components/DeleteBulkPrompt";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import Button from "primevue/button";

const props = defineProps({
    categories:Object,
    product_type:Object,
    filters:Object,
    sortables:{
        type:Array,
        default:[]
    }
});

const selectedItems = ref([]);
const metaKey = ref(true);
const selectedCategory = ref(null);

const resetSelectedItems = () => {
    selectedItems.value = [];
}

const resetSelectedCategory = () => {
    selectedCategory.value = null;
}

const productTypeHasLanguage = (lang) => {

    let data = null;
    const ptypefilter = props.product_type.connected_product_types.filter((x) => x.language == lang);

    if( ptypefilter.length ){
        data = ptypefilter[0];
    }
    return data;

}

const hasConnectedLanguage = (content,lang) => {

    let data = null;
    const dfilter = content.connected_product_categories.filter((x) => x.language == lang);

    if( dfilter.length ){
        data = dfilter[0];
    }
    return data;

}

</script>