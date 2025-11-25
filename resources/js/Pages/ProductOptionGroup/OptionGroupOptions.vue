<template>
    
    <div>

        <Dialog @hide="hide" v-model:visible="visible" modal :header="`${props.optionGroup.name} Seçenekleri`" :style="{ width: '50vw' }" :breakpoints="{ '960px': '75vw', '641px': '95vw' }">

            <div class="mb-5">
                <OptionGroupOptionsNew :option-group="props.optionGroup" :store-done="addToOptionsList"/>
            </div>
            <DataTable :value="options" @rowReorder="onRowReorder" stripedRows dataKey="id" tableStyle="min-width: 50rem">
                <template #empty> Seçilen kriterlere uygun kayıt bulunamadı. </template>
                <Column rowReorder headerStyle="width: 3rem" :reorderableColumn="false" />
                <Column field="name" header="İsim"></Column>
                <Column field="option_type" header="Tip"></Column>
                <Column class="text-end">
                    <template #body="{ data }">
                        <div class="d-flex flex-nowrap justify-content-end">
                            <OptionGroupOptionsEdit :option-group="props.optionGroup" :option-group-option="data" :update-done="refreshUpdatedItem"/>
                            <DeletePrompt :uri="route('product-option-group-options.delete',[data,{language:data.language}])" :model="data" :delete-done="removeDeletedItem"/>
                        </div>
                    </template>
                </Column>
            </DataTable>

        </Dialog>
    </div>

</template>

<script setup>

import {ref} from "vue";
import {usePage} from "@inertiajs/vue3";
import Dialog from "primevue/dialog";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import OptionGroupOptionsNew from "./OptionGroupOptionsNew";
import OptionGroupOptionsEdit from "./OptionGroupOptionsEdit";
import DeletePrompt from "../../Components/DeletePrompt";

const props = defineProps({
    optionGroup:Object,
    onHide:{
        type:Function,
        default:() => {}
    }
});

const visible = ref(true);
const options = ref(props.optionGroup.options);

const hide = () => {
    props.onHide();
    visible.value = false;
}

const addToOptionsList = (option) => {
    options.value = [...options.value, option];
}

const refreshUpdatedItem = (option) => {
    let item = options.value.find(x => x.id == option.id);
    Object.assign(item, option);
}

const removeDeletedItem = (option) => {
    let optionIndex = options.value.indexOf(option);
    options.value.splice(optionIndex,1);
}

const onRowReorder = (event) => {
    options.value = event.value;

    (async () => {
        const rawResponse = await fetch(route('product-option-group-options.reorder'), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': usePage().props.csrf_token
            },
            body: JSON.stringify({
                data:options.value.map((row) => row.id),
            })
        });
        const response = await rawResponse.json();
        console.log(response);
    })();

}

</script>