<template>
    <Head head-key="title" title="Ürün Tipi Müşteri Seçenekleri" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            
            <PageTitleWithLanguage :routeName="'product-customer-attributes.index'" :title="'Ürün Tipi Müşteri Seçenek'"/>

        </div>
    </div>

	
    <div id="kt_app_content" class="app-content  flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <div class="card">

                <div class="card-header px-4">
                    <div class="card-toolbar w-100 justify-content-between gap-2">
                        <NewProductCustomerAttribute :product-types="props.product_types.filter((x) => x.language == $page.props.current_language)" :language="$page.props.current_language"/>
                    </div>
                </div>

                <div class="card-body p-0">

                    <DataTable :value="props.product_customer_attributes.data" stripedRows dataKey="id" tableStyle="min-width: 50rem">
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
                                        <NewProductCustomerAttribute :button-class="'btn fw-bold btn-sm btn-icon btn-light-danger text-uppercase'" :button-text="lang" :product-types="props.product_types.filter((x) => x.language == lang)" :language="lang" :from-attribute="data" v-if="!hasConnectedLanguage(data,lang)" v-tooltip.top="'Oluştur'"/>
                                        <button type="button" v-if="hasConnectedLanguage(data,lang)" @click="selectedAttribute = hasConnectedLanguage(data,lang)" class="btn fw-bold btn-sm btn-icon btn-light-info text-uppercase" v-tooltip.top="'Güncelle'">{{ lang }}</button>
                                    </div>
                                </div>
                            </template>
                        </Column>
                        <Column field="name" header="İsim"></Column>
                        <Column field="slug" header="Slug"></Column>
                        <Column field="desc" header="Açıklama"></Column>
                        <Column field="option_type" header="Tip"></Column>
                        <Column header="Zorunlu">
                            <template #body="{ data }">
                                <span>{{ data.is_required ? 'Evet' : 'Hayır' }}</span>
                            </template>
                        </Column>
                        <Column class="text-end">
                            <template #body="{ data }">
                                <div class="d-flex flex-nowrap justify-content-end">
                                    <Button icon="bi bi-pen" rounded severity="success" @click="selectedAttribute = data" v-tooltip.top="'Güncelle'"/>
                                    <DeletePrompt :uri="route('product-customer-attributes.delete',[data.id,{language:$page.props.current_language}])" :model="data"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                </div>
                <div class="card-footer">
                    <Pagination :links="props.product_customer_attributes.links" :only="['product_customer_attributes','flash']"/>
                </div>
            </div>

        </div>

    </div>

    <EditProductCustomerAttribute :item="selectedAttribute" :product-types="props.product_types.filter((x) => x.language == selectedAttribute.language)" :on-hide="resetSelectedAttribute" v-if="selectedAttribute"/>

</template>

<script setup>

import {ref} from "vue";

import PageTitleWithLanguage from "../../Components/PageTitleWithLanguage";

import NewProductCustomerAttribute from "./NewProductCustomerAttribute";
import EditProductCustomerAttribute from "./EditProductCustomerAttribute";
import Pagination from "../../Components/Pagination";
import DeletePrompt from "../../Components/DeletePrompt";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import Button from "primevue/button";

const props = defineProps({
    product_customer_attributes:Object,
    product_types:Object
});

const selectedAttribute = ref(null);

const resetSelectedAttribute = () => {
    selectedAttribute.value = null;
}

const hasConnectedLanguage = (content,lang) => {

    let data = null;
    const dfilter = content.connected_product_customer_attributes.filter((x) => x.language == lang);

    if( dfilter.length ){
        data = dfilter[0];
    }
    return data;

}

</script>