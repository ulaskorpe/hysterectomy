<template>
    <Head head-key="title" title="Kampanyalar" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            
            <PageTitleWithLanguage :routeName="'campaigns.index'" :title="'Kampanya'"/>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content flex-column-fluid">

        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <div class="card">

                <div class="card-header px-4">
                    <div class="card-toolbar w-100 justify-content-between gap-2">
                        <Link :href="route('campaigns.create',{language:$page.props.current_language})" class="btn fw-bold btn-sm btn-success d-flex align-items-center">
                            <i class="bi bi-plus-circle fs-4"></i>
                            <span class="ms-1 lh-1">YENİ</span>
                        </Link>
                        <Link :href="route('campaigns.trash',{language:$page.props.current_language})" class="btn fw-bold btn-sm btn-light-danger btn-icon" v-tooltip.top="'Çöp Kutusu'">
                            <i class="bi bi-trash fs-4"></i>
                        </Link>
                    </div>
                </div>

                <div class="card-header px-4" v-if="selectedItems.length === 0">
                    <Filter :filters="props.filters" :uri="route('campaigns.index')"/>
                </div>
                <div class="card-header px-4" v-if="selectedItems.length > 0">
                    <h5 class="card-title fs-6"><i class="bi bi-check2-circle me-2 fs-2"></i>{{ selectedItems.length }}</h5>
                    <div class="card-toolbar">
                        <DeleteBulkPrompt :uri="route('campaigns.bulk.delete')" :items="selectedItems"/>
                    </div>
                </div>
                <div class="card-body p-0">

                    <DataTable v-model:selection="selectedItems" :value="props.campaigns.data" stripedRows dataKey="id" tableStyle="min-width: 50rem">
                        <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                        <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
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
                                        <Link v-if="!hasConnectedLanguage(data,lang)" :href="route('campaigns.create',{language:lang, uuid:data.uuid, from_id:data.id})" class="btn fw-bold btn-sm btn-icon btn-light-danger text-uppercase" v-tooltip.top="'Oluştur'">{{ lang }}</Link>
                                        <Link v-if="hasConnectedLanguage(data,lang)" :href="route('campaigns.edit',[hasConnectedLanguage(data,lang).id,{language:lang}])" class="btn fw-bold btn-sm btn-icon btn-light-info text-uppercase" v-tooltip.top="'Güncelle'">{{ lang }}</Link>
                                    </div>
                                </div>
                            </template>
                        </Column>
                        <Column header="İsim">
                            <template #body="{ data }">
                                <span>{{ data.name }}</span>
                            </template>
                        </Column>
                        <Column field="discount" header="İndirim">
                            <template #body="{ data }">
                                <div class="d-flex gap-1 align-items-center">
                                    <span>{{ data.discount }}</span>
                                    <span v-if="data.type == 'fixed'">TRY</span>
                                    <span v-else>%</span>
                                </div>
                            </template>
                        </Column>
                        <Column field="users_only" header="Üyelere Özel"></Column>
                        <Column field="apply_cart" header="Sepette Geçerli"></Column>
                        <Column field="min_cart_amount" header="Min Sepet Tutarı"></Column>
                        <Column field="product_types" header="Geçerli Ürün Tipleri">
                            <template #body="{ data }">
                                <div class="vstack">
                                    <span v-for="(ptype,p) in data.product_types" :key="p">{{ ptype.name }}</span>
                                </div>
                            </template>
                        </Column>
                        <Column field="start_date" header="Başlangıç">
                            <template #body="{ data }">
                                <span v-html="formatDateToMask(data.start_date)"></span>
                            </template>
                        </Column>
                        <Column field="end_date" header="Bitiş">
                            <template #body="{ data }">
                                <span v-html="formatDateToMask(data.end_date)"></span>
                            </template>
                        </Column>
                        <Column class="text-end">
                            <template #body="{ data }">
                                <div class="d-flex flex-nowrap justify-content-end">
                                    <a :href="`${data.uri.final_uri}`" target="_blank" class="ms-2 btn btn-sm text-success btn-link">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <Link :href="route('campaigns.edit',[data.id,{language:$page.props.current_language}])">
                                        <Button icon="bi bi-pencil" text rounded severity="info" class="ms-2"/>
                                    </Link>
                                    <DeletePrompt :delete-done="resetSelectedItems" :uri="route('campaigns.delete',[data.id,{language:$page.props.current_language}])" :model="data"/>
                                </div>
                            </template>
                        </Column>
                    </DataTable>

                </div>
                <div class="card-footer">
                    <Pagination :links="props.campaigns.links" :only="['campaigns','flash']"/>
                </div>
            </div>

        </div>

    </div>

</template>

<script setup>

import {ref} from "vue";
import PageTitleWithLanguage from "../../Components/PageTitleWithLanguage";

import DeletePrompt from "../../Components/DeletePrompt";
import DeleteBulkPrompt from "../../Components/DeleteBulkPrompt";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import Filter from "../../Components/Filter";
import Button from "primevue/button";
import Pagination from "../../Components/Pagination";

const props = defineProps({
    campaigns:Object,
    filters:Object,
    sortables:{
        type:Array,
        default:[]
    }
});

const selectedItems = ref([]);
const metaKey = ref(true);
const selectedCampaign = ref(null);

const resetSelectedItems = () => {
    selectedItems.value = [];
}

const resetselectedCampaign = () => {
    selectedCampaign.value = null;
}

const formatDateToMask = (date) => {
    var originalDate = new Date(date);
    var day = originalDate.getDate().toString().padStart(2, '0');
    var month = (originalDate.getMonth() + 1).toString().padStart(2, '0'); // Ay 0-11 arasında olduğu için +1 ekliyoruz.
    var year = originalDate.getFullYear();

    return day + '.' + month + '.' + year;
}

const hasConnectedLanguage = (content,lang) => {

    let data = null;
    const dfilter = content.connected_campaigns.filter((x) => x.language == lang);

    if( dfilter.length ){
        data = dfilter[0];
    }
    return data;

}

</script>