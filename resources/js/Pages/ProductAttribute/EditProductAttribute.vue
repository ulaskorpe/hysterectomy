<template>
    
    <div>
        <Dialog @hide="hide" v-model:visible="visible" modal header="Ürün Tipi Seçenek Güncelle" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="update" class="d-flex flex-column h-100">

                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center mb-1">
                        <span class="fw-semibold">İsim</span>
                        <div class="d-flex align-items-center ms-2">
                            <img :src="`/dmn/media/flags/${props.item.language}.svg`" class="me-2" style="width: 14px" />
                            <span class="text-uppercase">{{ props.item.language }}</span>
                        </div>
                    </div>
                    <InputText v-model="form.name" class="w-100" />
                </div>

                <Divider />

                <div class="d-flex flex-column">
                    <label class="form-label">Tip</label>
                    <Dropdown v-model="form.option_type" :options="$page.props.enums.option_types" optionLabel="label" optionValue="value" placeholder="Seçiniz" class="w-100" />
                </div>

            
                <div class="d-flex flex-column" v-if="form.option_type == 'select' || form.option_type == 'multiselect'">
                    <Divider />
                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <span class="fw-semibold">Seçenekler</span>
                            <div class="d-flex align-items-center ms-2">
                                <img :src="`/dmn/media/flags/${form.language}.svg`" class="me-2" style="width: 14px" />
                                <span class="text-uppercase">{{ form.language }}</span>
                            </div>
                        </div>
                        <div v-for="(val,key) in form.values" :key="key">
                            <div class="border rounded bg-opacity-10 bg-dark p-2 d-flex flex-row mb-1">
                                <div class="flex-grow-1">
                                    <div class="row w-100">
                                        <div :class="{'col-md-12':form.site_view == '','col-md-8':form.site_view != ''}">
                                            <InputText v-model="val.name" class="w-100" placeholder="İsim" size="small"/>
                                        </div>
                                        <div class="col-md-4" v-if="form.site_view == 'color'">
                                            <div class="hstack gap-2 align-items-center h-100">
                                                <span>Renk</span>
                                                <ColorPicker v-model="val.color_value" />
                                            </div>
                                        </div>
                                        <div class="col-md-4" v-if="form.site_view == 'image'">
                                            <div class="d-flex flex-row flex-wrap align-items-center">
                                                <img :src="val.image_uri" v-if="val.image_uri" class="img-thumbnail" width="50"/>
                                                <div class="d-flex flex-row align-items-center">
                                                    <PopupMediaLibrary v-if="!val.image_uri" @click="optionValueIndex = key" :on-select="setOptionValueMedia" :form-model="val.image_uri"  :button-text="'Seç'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>
                                                    <button type="button" class="btn btn-sm btn-light-danger btn-icon ms-2" v-if="val.image_uri" @click="val.image_uri = null">
                                                        <i class="bi bi-x fs-3"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-icon text-danger ms-2" @click="form.values.splice(key,1)"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-dark px-2 py-1 mt-4" @click="addOptionValue">Seçenek Ekle</button>

                    </div>
                </div>

                <Divider />

                <div class="d-flex flex-column mb-10">
                    <label class="form-label">Kullanacak Ürün Tipleri</label>
                    <MultiSelect v-model="form.product_types" :options="props.productTypes" optionLabel="name" optionValue="id" placeholder="Lütfen seçiniz" :maxSelectedLabels="5" class="w-100" />
                </div>

                <div class="row g-5">
                    <div class="col-lg-4 d-flex flex-row">
                        <InputSwitch v-model="form.fe_visible" />
                        <label class="fw-bold ms-2">Müşteriler görebilir</label>
                    </div>
                    <div class="col-lg-4 d-flex flex-row">
                        <InputSwitch v-model="form.is_required" />
                        <label class="fw-bold ms-2">Zorunlu</label>
                    </div>
                    <div class="col-lg-4 d-flex flex-row">
                        <InputSwitch v-model="form.unit_scale" />
                        <label class="fw-bold ms-2">Fiyat Birim Ölçeği</label>
                    </div>
                </div>

                <div class="separator my-10"></div>

                <div class="">
                    <button type="submit" class="btn btn-primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Kaydet</button>
                    <button type="button" class="btn btn-light ms-5" @click="cancel()">Vazgeç</button>
                </div>
                <FormErrors :form="form"/>
            </form>
        </Dialog>
    </div>

</template>

<script setup>

import {ref} from "vue";
import { useForm } from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import InputText from 'primevue/inputtext';
import InputSwitch from 'primevue/inputswitch';
import MultiSelect from "primevue/multiselect";
import Dropdown from "primevue/dropdown";
import Divider from "primevue/divider";
import ColorPicker from "primevue/colorpicker";
import PopupMediaLibrary from "../MediaLibrary/PopupMediaLibrary";

const props = defineProps({
    item:Object,
    productTypes:Object,
    onHide:{
        type:Function,
        default:() => {}
    }
});

const visible = ref(true);
const optionValueIndex = ref(null);

const form = useForm({
    name:props.item.name,
    language:props.item.language,
    option_type:props.item.option_type,
    fe_visible:props.item.fe_visible,
    is_required:props.item.is_required,
    product_types:props.item.product_types.map((row) => row.id),
    values:props.item.values.map((row) => ({
        id:row.id,
        name:row.name,
        color_value:row.color_value,
        image_uri:row.image_uri
    })),
    site_view:props.item.site_view,
    unit_scale:props.item.unit_scale,
});

const cancel = () => {
    visible.value = false;
    form.reset();
};

const addOptionValue = () => {

    form.values.push({
        name:"",
        color_value:"000000",
        image_uri:"",
    });

}

const update = () => {
    form.put(route('product-attributes.update',[props.item,{language:props.item.language}]),{
        preserveScroll: true,
        onSuccess: (page) => {
            visible.value = false;
        }
    });
}

const hide = () => {
    visible.value = false;
    props.onHide();
}

const setOptionValueMedia = (media) => {
    form.values[optionValueIndex.value].image_uri = media.original_url;
}

</script>