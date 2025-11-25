<template>
    
    <div>
        <Button icon="bi bi-pencil text-primary" text rounded severity="info" @click="dialogVisible = true" class="ms-2"/>

        <Dialog v-model:visible="dialogVisible" modal :header="`${props.optionGroup.name} için yeni seçenek ekle`" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">
            <form @submit.prevent="update">

                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-1">
                                <span class="fw-semibold">İsim</span>
                                <div class="d-flex align-items-center ms-2">
                                    <img :src="`/dmn/media/flags/${props.optionGroup.language}.svg`" class="me-2" style="width: 14px" />
                                    <span class="text-uppercase">{{ props.optionGroup.language }}</span>
                                </div>
                            </div>
                            <InputText v-model="form.name" class="w-100" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center mb-1">
                                <span class="fw-semibold">Tür</span>
                                <div class="d-flex align-items-center ms-2">
                                    <img :src="`/dmn/media/flags/${props.optionGroup.language}.svg`" style="width: 14px" />
                                </div>
                            </div>
                            <Dropdown @change="canMultiselectType" v-model="form.option_type" :options="$page.props.enums.option_types" optionLabel="label" optionValue="value" placeholder="Seçiniz" class="w-100" />
                        </div>
                    </div>
                </div>
                
                <div class="d-flex flex-column" v-if="form.option_type == 'select' || form.option_type == 'multiselect' || form.option_type == 'radio'">
                    <Divider />

                    <div class="row align-items-center">
                        <label class="form-label col-6">Site görünümü</label>
                        <div class="col-6">
                            <Dropdown v-model="form.site_view" :options="$page.props.enums.option_site_views" optionLabel="label" optionValue="value" class="w-100" placeholder="Default"/>
                        </div>
                    </div>

                    <Divider />

                    <div>
                        <div class="d-flex align-items-center mb-1">
                            <span class="fw-semibold">Seçenekler</span>
                            <div class="d-flex align-items-center ms-2">
                                <img :src="`/dmn/media/flags/${props.optionGroup.language}.svg`" class="me-2" style="width: 14px" />
                                <span class="text-uppercase">{{ props.optionGroup.language }}</span>
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
                
                <div class="row g-5">
                    <div class="col-lg-4 d-flex flex-row">
                        <InputSwitch v-model="form.fe_visible" />
                        <label class="fw-bold ms-2">Müşteriler görebilir</label>
                    </div>
                </div>
                <div class="separator my-10"></div>
                <button type="submit" class="btn btn-primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Kaydet</button>
                <FormErrors :form="form"/>

            </form>
        </Dialog>
    </div>

</template>

<script setup>

import {ref} from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import InputText from 'primevue/inputtext';
import InputSwitch from 'primevue/inputswitch';
import Dropdown from 'primevue/dropdown';
import Button from "primevue/button";
import Divider from "primevue/divider";
import ColorPicker from "primevue/colorpicker";
import PopupMediaLibrary from "../MediaLibrary/PopupMediaLibrary";

const props = defineProps({
    optionGroup:Object,
    optionGroupOption:Object,
    updateDone:{
        type:Function,
        default:() => {}
    }
});

const dialogVisible = ref(false);
const optionValueIndex = ref(null);

const form = useForm({
    name:props.optionGroupOption.name,
    language:props.optionGroupOption.language,
    product_option_group_id:props.optionGroup.id,
    fe_visible:props.optionGroupOption.fe_visible,
    option_type:props.optionGroupOption.option_type,
    values:props.optionGroupOption.option_values.map((row) => ({
        id:row.id,
        name:row.name,
        color_value:row.color_value,
        image_uri:row.image_uri
    })),
    site_view:props.optionGroupOption.site_view
});

//optionGroup vartiant select type step_byt_step olmazsa, multiselect sorun yaratir. Bu nedenle multiselect sectirmeyecegiz.
const canMultiselectType = () => {

    if( props.optionGroup.variant_select_type != 'step_by_step' && form.option_type == 'multiselect' ){
        alert('Seçenek grubu sadece "step_by_step" ise multiselect kullanılabilir.');
        form.option_type = 'select';
    }

}

const addOptionValue = () => {

    form.values.push({
        id:"",
        name:"",
        color_value:"000000",
        image_uri:""
    });

}

const update = () => {
    form.put(route('product-option-group-options.update',props.optionGroupOption),{
        onSuccess: () => {
            props.updateDone(usePage().props.flash.data);
            dialogVisible.value = false;
        },
    });
}

const setOptionValueMedia = (media) => {
    form.values[optionValueIndex.value].image_uri = media.original_url;
}

</script>