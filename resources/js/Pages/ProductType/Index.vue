<template>
    <Head head-key="title" title="Ürün Tipleri" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            
            <PageTitleWithLanguage :routeName="'product-types.index'" :title="'Ürün Tipi'"/>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content  flex-column-fluid">

        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <div class="card">

                <div class="card-header px-4">
                    <div class="card-toolbar w-100 justify-content-between gap-2">
                        <Link :href="route('product-types.create',{language:$page.props.current_language})" class="btn fw-bold btn-sm btn-success d-flex align-items-center">
                            <i class="bi bi-plus-circle fs-4"></i>
                            <span class="ms-1 lh-1">YENİ</span>
                        </Link>
                    </div>
                </div>

                <div class="card-header px-4" v-if="selectedItems.length === 0">
                    <div class="card-title me-auto">
                        <input type="text" class="form-control form-control-sm" placeholder="Ara..." v-model="dtFilters['global'].value">
                    </div>
                    <Filter :filters="props.filters" :uri="route('product-types.index')" :has-search="false"/>
                </div>
                <div class="card-header px-4" v-if="selectedItems.length > 0">
                    <h5 class="card-title fs-6"><i class="bi bi-check2-circle me-2 fs-2"></i>{{ selectedItems.length }}</h5>
                    <div class="card-toolbar">
                        <DeleteBulkPrompt :uri="route('product-types.bulk.delete')" :items="selectedItems"/>
                    </div>
                </div>
                <div class="card-body p-0">

                    <DataTable v-model:filters="dtFilters" v-model:selection="selectedItems" :value="props.product_types" stripedRows dataKey="id" tableStyle="min-width: 50rem">
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
                                        <Link v-if="!hasConnectedLanguage(data,lang)" :href="route('product-types.create',{language:lang, uuid:data.uuid, from_id:data.id})" class="btn fw-bold btn-sm btn-icon btn-light-danger text-uppercase" v-tooltip.top="'Oluştur'">{{ lang }}</Link>
                                        <Link v-if="hasConnectedLanguage(data,lang)" :href="route('product-types.edit',[hasConnectedLanguage(data,lang).id,{language:lang}])" class="btn fw-bold btn-sm btn-icon btn-light-info text-uppercase" v-tooltip.top="'Güncelle'">{{ lang }}</Link>
                                    </div>
                                </div>
                            </template>
                        </Column>
                        <Column header="İsim">
                            <template #body="{ data }">
                                <span>{{ data.name }}</span>
                            </template>
                        </Column>
                        <Column field="slug" header="Slug"></Column>
                        <Column class="text-end">
                            <template #body="{ data }">
                                <div class="d-flex flex-nowrap justify-content-end">
                                    <Link :href="route('product-types.edit',[data.id,{language:$page.props.current_language}])">
                                        <Button icon="bi bi-pencil" text rounded severity="info" class="ms-2"/>
                                    </Link>
                                    <DeletePrompt :delete-done="resetSelectedItems" :uri="route('product-types.delete',[data.id,{language:$page.props.current_language}])" :model="data"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                </div>
            </div>

        </div>

    </div>

</template>

<script setup>

import {ref} from "vue";
import DeletePrompt from "../../Components/DeletePrompt";
import DeleteBulkPrompt from "../../Components/DeleteBulkPrompt";
import DataTable from "primevue/datatable";
import { FilterMatchMode } from 'primevue/api';
import Column from 'primevue/column';
import Filter from "../../Components/Filter";
import Button from "primevue/button";
import PageTitleWithLanguage from "../../Components/PageTitleWithLanguage";

const props = defineProps({
    product_types:Object,
    filters:Object,
    sortables:{
        type:Array,
        default:[]
    }
});

const selectedItems = ref([]);
const metaKey = ref(true);

const resetSelectedItems = () => {
    selectedItems.value = [];
}

const dtFilters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const hasConnectedLanguage = (content,lang) => {

    let data = null;
    const dfilter = content.connected_product_types.filter((x) => x.language == lang);

    if( dfilter.length ){
        data = dfilter[0];
    }
    return data;

}
</script>