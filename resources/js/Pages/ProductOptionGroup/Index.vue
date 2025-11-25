<template>
    <Head head-key="title" title="Ürün Seçenek Grupları" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            
            <PageTitleWithLanguage :routeName="'product-option-groups.index'" :title="'Ürün Seçenek Grup'"/>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content  flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            <div class="card">

                <div class="card-header px-4">
                    <div class="card-toolbar w-100 justify-content-between gap-2">
                        <NewProductOptionGroup :product-types="props.product_types" :language="$page.props.current_language"/>
                    </div>
                </div>

                <div class="card-body p-0">

                    <DataTable :value="props.product_option_groups.data" stripedRows dataKey="id" tableStyle="min-width: 50rem">
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
                                        <NewProductOptionGroup :button-class="'btn fw-bold btn-sm btn-icon btn-light-danger text-uppercase'" :button-text="lang" :product-types="props.product_types" :language="lang" :from-group="data" v-if="!hasConnectedLanguage(data,lang)"  v-tooltip.top="'Oluştur'"/>
                                        <button type="button" v-if="hasConnectedLanguage(data,lang)" @click="selectedGroupForEdit = hasConnectedLanguage(data,lang); editVisible = true" class="btn fw-bold btn-sm btn-icon btn-light-info text-uppercase" v-tooltip.top="'Güncelle'">{{ lang }}</button>
                                    </div>
                                </div>
                            </template>
                        </Column>
                        <Column field="name" header="İsim"></Column>
                        <Column field="slug" header="Slug"></Column>
                        <Column class="text-end">
                            <template #body="{ data }">
                                <div class="d-flex flex-nowrap justify-content-end gap-2">
                                    <Button icon="bi bi-gear" rounded severity="success" @click="selectedGroup = data" v-tooltip.top="'Seçenekleri Düzenle'"/>
                                    <Button icon="bi bi-pencil text-primary" text rounded severity="info" @click="selectedGroupForEdit = data; editVisible = true" v-tooltip.top="'Güncelle'"/>
                                    <DeletePrompt :uri="route('product-option-groups.delete',[data.id,{language:$page.props.current_language}])" :model="data"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                </div>
                <div class="card-footer position-sticky bottom-0 bg-white">
                    <PaginationSticky :links="props.product_option_groups.links" :only="['product_option_groups','flash']"/>
                </div>
            </div>

        </div>

    </div>

    <OptionGroupOptions :option-group="selectedGroup" :on-hide="resetSelectedGroup" v-if="selectedGroup"/>

    <Dialog v-model:visible="editVisible" @hide="updateDone" modal header="Ürün Seçenek Grubu Güncelle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
        <EditProductOptionGroup :item="selectedGroupForEdit" :product-types="props.product_types" :on-update-done="updateDone" v-if="selectedGroupForEdit"/>
    </Dialog>

</template>

<script setup>

import {ref} from "vue";
import NewProductOptionGroup from "./NewProductOptionGroup";
import EditProductOptionGroup from "./EditProductOptionGroup";
import OptionGroupOptions from "./OptionGroupOptions";
import PaginationSticky from "../../Components/PaginationSticky";
import DeletePrompt from "../../Components/DeletePrompt";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import Button from "primevue/button";
import Dialog from "primevue/dialog";

import PageTitleWithLanguage from "../../Components/PageTitleWithLanguage";

const props = defineProps({
    product_option_groups:Object,
    product_types:Object
});

const selectedGroup = ref(null);
const selectedGroupForEdit = ref(null);
const editVisible = ref(false);

const resetSelectedGroup = () => {
    selectedGroup.value = null;
}

const updateDone = () => {
    selectedGroupForEdit.value = null;
    editVisible.value = false;
}

const hasConnectedLanguage = (content,lang) => {

    let data = null;
    const dfilter = content.connected_product_option_groups.filter((x) => x.language == lang);

    if( dfilter.length ){
        data = dfilter[0];
    }
    return data;

}
</script>