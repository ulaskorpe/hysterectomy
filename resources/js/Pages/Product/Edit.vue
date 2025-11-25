<template>
    <Head head-key="title" :title="`${props.product_type.name} listesi`" />

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
            
            <div  class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <span class="text-muted fs-6">{{ props.product_type.name }}</span>
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Güncelle: {{ props.product.name }}
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link :href="route('products.index',{productType:props.product_type.id})" class="btn fw-bold btn-sm btn-secondary d-flex align-items-center">
                    <i class="bi bi-arrow-left fs-4"></i>
                    <span class="ms-1 lh-1">Geri</span>
                </Link>
            </div>

        </div>
    </div>
	
    <div id="kt_app_content" class="app-content  flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-fluid">
            
            <FormErrors :form="form"/>

            <form @submit.prevent="update">

                <div class="row g-4 g-lg-6 g-xl-10">
                    <div class="col-lg-9">

                        <div class="card mb-4 mb-xl-10">
                            <div class="card-body">

                                <h5 class="card-title text-muted">{{ props.product_type.name }} Genel Bilgiler</h5>
                                <Divider class="mb-10"/>

                                <div class="row align-items-center">
                                    <label class="col-md-3 fw-bold text-uppercase">SKU</label>
                                    <div class="col-md-9">
                                        <InputText v-model="form.sku" :class="{'p-invalid':form.errors.sku}"/>
                                    </div>
                                </div>

                                <Divider />

                                <div class="row align-items-center">
                                    <label class="col-md-3 fw-bold text-uppercase">İsim</label>
                                    <div class="col-md-9">
                                        <InputText class="w-100" v-model="form.name" :class="{'p-invalid':form.errors.name}"/>
                                    </div>
                                </div>

                                <div v-if="props.categories.length">
                                    <Divider />
                                    <div class="row align-items-center">
                                        <label class="col-md-3 fw-bold text-uppercase">kategori</label>
                                        <div class="col-md-9">
                                            <MultiSelect v-model="form.product_categories" :options="props.categories" optionLabel="depth_name" optionValue="id" placeholder="Lütfen seçiniz" :maxSelectedLabels="5" class="w-100" :class="{'p-invalid':form.errors.product_categories}"/>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="props.product_attributes && props.product_attributes.length > 0" class="vstack gap-2">
                                    <Divider />
                                    <div class="row align-items-center" v-for="(attribute, index) in form.attributes" :key="`product-attribute-${index}`">
                                        <label class="col-md-3 fw-bold text-uppercase">{{ attribute.name }}</label>
                                        <div class="col-md-9">
                                            
                                            <InputMask mask="99:99" v-if="attribute.option_type == 'time'" v-model="attribute.value" :class="{'p-invalid':form.errors[`attributes.${index}.value`]}"/>
                                            
                                            <InputMask mask="99.99.9999" placeholder="GG.AA.YYYY" v-if="attribute.option_type == 'date'" v-model="attribute.value" :class="{'p-invalid':form.errors[`attributes.${index}.value`]}"/>
                                            
                                            <InputMask mask="99.99.9999 99:99" placeholder="GG.AA.YYYY HH:SS" v-if="attribute.option_type == 'datetime'" v-model="attribute.value" :class="{'p-invalid':form.errors[`attributes.${index}.value`]}"/>
                                            
                                            <Dropdown v-if="attribute.option_type == 'select'" v-model="attribute.value" :options="attribute.values" optionLabel="name" optionValue="id" placeholder="Lütfen seçiniz" :class="{'p-invalid':form.errors[`attributes.${index}.value`]}"/>
                                            <MultiSelect v-if="attribute.option_type == 'multiselect'" v-model="attribute.value" :options="attribute.values" optionLabel="name" optionValue="id" placeholder="Lütfen seçiniz" :class="{'p-invalid':form.errors[`attributes.${index}.value`]}"/>
                                            
                                            <div class="d-flex flex-row align-items-center" v-if="attribute.option_type == 'file'">
                                                <PopupMediaLibrary v-if="!attribute.value" :on-select="setAttributeMedia" :form-model="attribute" :button-text="'Seç'" :mime-type="'application/'" :key="Math.floor(Math.random() * 100000)"/>
                                                <div class="d-flex flex-" v-if="attribute.value">
                                                    <a target="_blank" :href="attribute.value" class="btn btn-sm btn-light-success">Göster</a>
                                                    <button type="button" class="btn btn-sm btn-light-danger btn-icon ms-2" @click="attribute.value = null">
                                                        <i class="bi bi-x fs-3"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <OptionType :option="attribute" v-model="attribute.value" v-else :error-class="form.errors[`attributes.${index}.value`] ? true : false"/>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="props.product_type.is_cartable">
                                    <Divider />
                                    <div class="row align-items-center">
                                        <label class="col-md-3 fw-bold text-uppercase">Fiyat</label>
                                        <div class="col-md-9">
                                            <InputNumber v-model="form.price" mode="currency" currency="TRY" locale="tr-TR" :class="{'p-invalid':form.errors.price}"></InputNumber>
                                        </div>
                                    </div>
                                    <Divider />
                                    <div class="row align-items-center">
                                        <label class="col-md-3 fw-bold text-uppercase">İndirim</label>
                                        <div class="col-md-9">
                                            <InputSwitch v-model="form.has_discount" />
                                            <div v-if="form.has_discount">
                                                <Divider />
                                                <div class="row align-items-center g-2">
                                                <div class="col-md-6">
                                                    <label class="fw-bold text-uppercase me-2">İndirim Tipi</label>
                                                    <Dropdown v-model="form.discount_type" :options="[{label:'percentage',value:'percentage'},{label:'fixed',value:'fixed'}]" optionLabel="label" optionValue="value" placeholder="Lütfen seçiniz" :class="{'p-invalid':form.errors.discount_type}"/>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="fw-bold text-uppercase me-2">Değer</label>
                                                    <InputNumber v-model="form.discount_value" :class="{'p-invalid':form.errors.discount_value}"></InputNumber>
                                                </div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="props.product_type.stock_usage">
                                    <Divider />
                                    <div class="row align-items-center">
                                        <label class="col-md-3 fw-bold text-uppercase">Stok</label>
                                        <div class="col-md-9">
                                            <InputNumber v-model="form.stock" :useGrouping="false" :class="{'p-invalid':form.errors.stock}"></InputNumber>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card mb-4 mb-xl-10" v-if="props.product_type.option_group">
                            <div class="card-body">
                                <h5 class="card-title text-muted">{{ props.product_type.name }} Seçenek Grup, Variable</h5>
                                <Divider class="mb-10"/>

                                <div v-if="props.product_type.option_group">
                                    <div class="row align-items-center">
                                        <label class="col-md-3 fw-bold text-uppercase">Seçenek Grup Kullanım</label>
                                        <div class="col-md-9">
                                            <InputSwitch v-model="form.use_option_group" />
                                        </div>
                                    </div>
                                    <div v-if="form.use_option_group">
                                        <Divider />
                                        <div class="row align-items-center">
                                            <label class="col-md-3 fw-bold text-uppercase">Kullanılacak Seçenekler</label>
                                            <div class="col-md-9">
                                                <MultiSelect v-model="form.additional.options_for_use" :options="props.product_type.option_group.options" optionValue="id" optionLabel="name" class="w-100"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div v-if="props.product_type.option_group && form.use_option_group">
                            <div v-if="form.option_group.options" class="card mb-4 mb-xl-10">
                                <div class="card-body">

                                    <div class="hstack gap-2 justify-content-between alig-items-center">
                                        <h5 class="card-title text-muted">{{ form.option_group.name }}</h5>
                                        <button @click="confirmAllCombinations()" type="button" class="btn btn-sm btn-light-danger">Tüm Varyasyonları Oluştur</button>
                                    </div>
                                    <Divider class="mb-10"/>

                                    <Accordion expandIcon="bi bi-chevron-down" collapseIcon="bi bi-chevron-up">
                                        <AccordionTab v-for="(optionRow,row) in form.option_group.options" :key="`group-option-${row}`">

                                            <template #header>
                                                <div class="d-flex align-items-center gap-2 w-100 justify-content-between">
                                                    <span class="">Varyasyon #{{row + 1}}</span>
                                                    <span 
                                                        @click="deleteOptionsRow(row)"
                                                        class="badge badge-circle badge-danger cursor-pointer">
                                                        <i class="bi bi-x text-white"></i>
                                                    </span>
                                                </div>
                                            </template>

                                            <div class="position-relative">

                                                <div class="row g-4 g-xl-7">

                                                    <div class="col-xl-6">
                                                        <div class="" v-for="(option,o) in optionRow.row_options.filter(x => form.additional.options_for_use.includes(x.id))" :key="`group-option-${o}`">
                                                            <Divider v-if="o != 0"/>
                                                            <div class="row g-3 align-items-center">
                                                                <label class="col-md-3 fw-bold text-uppercase">{{ option.name }}</label>
                                                                <div class="col-md-9">
                                                                    <InputMask v-if="option.option_type == 'time'" v-model="option.value" :class="{'p-invalid':form.errors[`option_group.options.${row}.row_options.${o}.value`]}" mask="99:99"/>
                                                                    <InputMask v-if="option.option_type == 'date'" v-model="option.value" :class="{'p-invalid':form.errors[`option_group.options.${row}.row_options.${o}.value`]}" mask="99.99.9999" placeholder="GG.AA.YYYY"/>
                                                                    <InputMask v-if="option.option_type == 'datetime'" v-model="option.value" :class="{'p-invalid':form.errors[`option_group.options.${row}.row_options.${o}.value`]}" mask="99.99.9999 99:99" placeholder="GG.AA.YYYY HH:SS"/>
                                                                    <Dropdown v-if="option.option_type == 'select' || option.option_type == 'radio'" v-model="option.value" :options="option.values" optionLabel="name" optionValue="name" placeholder="Lütfen seçiniz" :class="{'p-invalid':form.errors[`option_group.options.${row}.row_options.${o}.value`]}"/>
                                                                    <MultiSelect v-if="option.option_type == 'multiselect'" v-model="option.value" :options="option.values" optionLabel="name" optionValue="name" placeholder="Lütfen seçiniz" :class="{'p-invalid':form.errors[`option_group.options.${row}.row_options.${o}.value`]}"/>
                                                                    <div class="d-flex flex-row align-items-center" v-if="option.option_type == 'file'">
                                                                        <PopupMediaLibrary v-if="!option.value" @click="optionMediaObjectIndex = o; optionMediaObjectRowIndex=row" :on-select="setOptionGroupOptionMedia" :form-model="option" :button-text="'Seç'" :mime-type="'application/'" :key="Math.floor(Math.random() * 100000)"/>
                                                                        <div class="d-flex flex-" v-if="option.value">
                                                                            <a target="_blank" :href="option.value" class="btn btn-sm btn-light-success">Göster</a>
                                                                            <button type="button" class="btn btn-sm btn-light-danger btn-icon ms-2" @click="option.value = null">
                                                                                <i class="bi bi-x fs-3"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex flex-row flex-wrap align-items-center" v-if="option.option_type == 'image'">
                                                                        <img :src="option.value" v-if="option.value" class="img-thumbnail" width="100"/>
                                                                        <div class="d-flex flex-row align-items-center">
                                                                            <PopupMediaLibrary v-if="!option.value" @click="optionMediaObjectIndex = o; optionMediaObjectRowIndex=row" :on-select="setOptionGroupOptionMedia" :form-model="option"  :button-text="'Seç'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>
                                                                            <button type="button" class="btn btn-sm btn-light-danger btn-icon ms-2" v-if="option.value" @click="option.value = null">
                                                                                <i class="bi bi-x fs-3"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <OptionType :option="option" v-model="option.value" v-else :error-class="form.errors[`option_group.options.${row}.row_options.${o}.value`] ? true : false"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <div class="rounded bg-gray-100 border p-4 h-100">

                                                            <div class="">
                                                                <div class="d-flex flex-row flex-wrap align-items-end">
                                                                    <div class="symbol symbol-100px me-2">
                                                                        <img :src="optionRow.media_objects.mainImage.preview_url" v-if="optionRow.media_objects.mainImage"/>
                                                                        <div class="symbol-label bg-white" v-else>
                                                                            <i class="bi bi-image fs-2x"></i>
                                                                        </div>
                                                                    </div>
                                                                    <div class="d-flex flex-row align-items-center">
                                                                        <PopupMediaLibrary v-if="!optionRow.media_objects.mainImage" @click="optionMediaObjectRowIndex=row" :on-select="setOptionRowMedia" :form-model="optionRow.media_objects.mainImage" :button-text="'Seç'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>
                                                                        <button type="button" class="btn btn-sm btn-light-danger btn-icon ms-2" @click="optionRow.media_objects.mainImage = null" v-if="optionRow.media_objects.mainImage">
                                                                            <i class="bi bi-x fs-3"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div v-if="form.option_group.stock_usage">
                                                                <Divider />
                                                                <div class="">
                                                                    <label class="fw-bold text-uppercase">STOK</label>
                                                                    <div class="">
                                                                        <InputNumber class="w-100" v-model="optionRow.stock" :inputId="`stock-${row}`" :useGrouping="false" :class="{'p-invalid':form.errors[`option_group.options.${row}.stock`]}"/>
                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                            
                                                            <div class="" v-if="form.option_group.has_own_price">
                                                                <Divider />
                                                                <label class="fw-bold text-uppercase">fiyat</label>
                                                                <div class="">
                                                                    <InputNumber class="w-100" mode="currency" currency="TRY" locale="tr-TR" v-model="optionRow.price" :inputId="`price-${row}`" :useGrouping="false" :class="{'p-invalid':form.errors[`option_groups.options.${row}.price`]}"/>
                                                                </div>
                                                                <Divider />
                                                                <div class="hstack align-items-center gap-2">
                                                                    <InputSwitch v-model="optionRow.has_discount"/>
                                                                    <span class="fw-bold text-uppercase">İndirim</span>
                                                                </div>
                                                                <div v-if="optionRow.has_discount">
                                                                    <Divider />
                                                                    <div class="d-flex flex-row align-items-center justify-content-between">
                                                                        <label class="fw-bold text-uppercase">indirim tipi</label>
                                                                        <Dropdown v-model="optionRow.discount_type" :options="['percentage','fixed']"/>
                                                                    </div>
                                                                    <Divider />
                                                                    <div class="d-flex flex-row align-items-center justify-content-bew">
                                                                        <label class="fw-bold text-uppercase">değer</label>
                                                                        <InputNumber v-model="optionRow.discount_value" />
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                        </AccordionTab>
                                    </Accordion>

                                    <button type="button" class="btn btn-warning btn-sm mt-3" @click="addOptionGroupOptionsRow(props.option_group)">Yeni</button>

                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 mb-xl-10">
                            <div class="card-body">
                                <h5 class="card-title text-muted">SEO Bilgileri</h5>
                                <Divider class="mb-10"/>

                                <div class="row align-items-center">
                                    <label class="col-md-3 fw-bold text-uppercase">SEO Başlık</label>
                                    <div class="col-md-9">
                                        <InputText class="w-100" v-model="form.seo.title" :class="{'p-invalid':form.errors['seo.title']}"/>
                                    </div>
                                </div>

                                <div class="row align-items-center mt-5">
                                    <label class="col-md-3 fw-bold text-uppercase">SEO Açıklama</label>
                                    <div class="col-md-9">
                                        <Textarea autoResize rows="1" cols="30" class="w-100" v-model="form.seo.description" :class="{'p-invalid':form.errors['seo.description']}" />
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card mb-4 mb-xl-10" v-if="props.product_type.after_order_type == 'download'">
                            <div class="card-body">
                                <h5 class="card-title text-muted">İndirme Dosyası</h5>
                                <Divider class="mb-10"/>

                                <div class="row align-items-center">
                                    <label class="col-md-3 fw-bold text-uppercase">Dosya Adı</label>
                                    <div class="col-md-9">
                                        <InputText class="w-100" v-model="form.after_order_download_name" :class="{'p-invalid':form.errors.after_order_download_name}"/>
                                    </div>
                                </div>

                                <div class="row align-items-center mt-5">
                                    <label class="col-md-3 fw-bold text-uppercase">Dosya</label>
                                    <div class="col-md-9">
                                        <div class="d-flex flex-row align-items-center">
                                            <PopupMediaLibrary v-if="!form.after_order_download_file" :on-select="setDownloadableMedia" :button-text="'Dosya Seç'" :mime-type="'application/'" :key="Math.floor(Math.random() * 100000)"/>
                                            <div class="d-flex flex-" v-if="form.after_order_download_file">
                                                <a target="_blank" :href="form.after_order_download_file" class="btn btn-sm btn-light-success">Göster</a>
                                                <button type="button" class="btn btn-sm btn-light-danger btn-icon ms-2" @click="form.after_order_download_file = null">
                                                    <i class="bi bi-x fs-3"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card mb-4 mb-xl-10" v-if="props.product_type.after_order_type == 'email'">
                            <div class="card-body">
                                <h5 class="card-title text-muted">E-Posta Detayları</h5>
                                <Divider class="mb-10"/>

                                <div class="row align-items-center">
                                    <label class="col-md-3 fw-bold text-uppercase">E-posta konu başlığı</label>
                                    <div class="col-md-9">
                                        <InputText class="w-100" v-model="form.after_order_email_subject" :class="{'p-invalid':form.errors.after_order_email_subject}"/>
                                    </div>
                                </div>

                                <div class="row align-items-center mt-5">
                                    <label class="col-md-3 fw-bold text-uppercase">E-Posta İçeriği</label>
                                    <div class="col-md-9">
                                        <TextEditor v-model="form.after_order_email_body" />
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card mb-4 mb-xl-10">
                            <div class="card-body">

                                <h5 class="card-title text-muted">Özet Bilgi</h5>
                                <Divider class="mb-10"/>

                                <TextEditor v-model="form.summary" />

                            </div>
                        </div>

                        <div class="card mb-4 mb-xl-10">
                            <div class="card-body">

                                <h5 class="card-title text-muted">Detaylı Bilgi</h5>
                                <Divider class="mb-10"/>

                                <TextEditor v-model="form.description" :full-mode="true"/>

                            </div>
                        </div>

                        <ContentArea :form="form"/>

                        <div class="card mb-4 mb-xl-10">
                            <div class="card-body">

                                <h5 class="card-title text-muted">Header Scripts</h5>
                                <Divider class="mb-10"/>

                                <AceEditor
                                    v-model:codeContent="form.additional.headerScripts" 
                                    v-model:editor="editor"
                                    :options="{'showPrintMargin': false}"
                                    theme="chrome"
                                    lang="html"
                                    width="100%" 
                                    height="200px" 
                                />

                            </div>
                        </div>

                        <div class="card mb-4 mb-xl-10">
                            <div class="card-body">

                                <h5 class="card-title text-muted">Footer Scripts</h5>
                                <Divider class="mb-10"/>

                                <AceEditor
                                    v-model:codeContent="form.additional.footerScripts" 
                                    v-model:editor="editor"
                                    :options="{'showPrintMargin': false}"
                                    theme="chrome"
                                    lang="html"
                                    width="100%" 
                                    height="200px" 
                                />

                            </div>
                        </div>

                    </div>
                    <div class="col-lg-3">

                        <div class="card mb-4 mb-xl-10">
                            <div class="card-body">

                                <h5 class="card-title text-muted">Görsel</h5>
                                <Divider class="mb-10"/>
                                <div class="d-flex flex-row flex-wrap align-items-end mb-4 mb-xl-10">
                                    <div class="symbol symbol-100px me-2">
                                        <img :src="form.media_objects.mainImage.preview_url" v-if="form.media_objects.mainImage"/>
                                        <div class="symbol-label" v-else>
                                            <i class="bi bi-image fs-2x"></i>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-row align-items-center">
                                        <PopupMediaLibrary v-if="!form.media_objects.mainImage" :on-select="setMainImage" :button-text="'Seç'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>
                                        <button type="button" class="btn btn-sm btn-light-danger btn-icon ms-2" v-if="form.media_objects.mainImage" @click="form.media_objects.mainImage = null">
                                            <i class="bi bi-x fs-3"></i>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <h5 class="card-title text-muted">Başlık Alanı Görsel</h5>
                                    <Divider class="mb-5"/>
                                    <div class="mb-4 mb-xl-10">
                                        <div class="overlay overflow-hidden" v-if="form.media_objects.headerImage">
                                            <div class="overlay-wrapper">
                                                <img :src="form.media_objects.headerImage.original_url" class="img-fluid" />
                                            </div>
                                            <div class="overlay-layer bg-dark bg-opacity-25">
                                                <button type="button" class="btn btn-sm btn-light-danger btn-icon ms-2" v-if="form.media_objects.headerImage" @click="form.media_objects.headerImage = null">
                                                    <i class="bi bi-x fs-3"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <PopupMediaLibrary v-if="!form.media_objects.headerImage" :on-select="setHeaderImage" :button-text="'Seç'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>
                                    </div>
                                </div>

                                <h5 class="card-title text-muted">Galeri</h5>
                                <Divider class="mb-10"/>
                                <div v-if="form.media_objects.galleryImages" class="mb-4 mb-xl-10">
                                    
                                    <div class="row g-3 row-cols-3 mb-5">
                                        <div class="col" v-for="(media,m) in form.media_objects.galleryImages" :key="m">
                                            <div class="overlay overflow-hidden rounded">
                                                <div class="overlay-wrapper">
                                                    <img :src="media.preview_url" class="img-fluid rounded cursor-pointer" />
                                                </div>
                                                <div class="overlay-layer bg-dark bg-opacity-25">
                                                    <button type="button" class="btn btn-light-danger btn-sm btn-icon" @click="removeFromGallery(m)"><i class="bi bi-x fs-3"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <PopupMediaLibrary :on-select="setGalleryImages" :multiple="true" :button-text="'Görselleri Seç'" :mime-type="'image/'" :key="Math.floor(Math.random() * 100000)"/>

                                </div>

                                <div>
                                    <h5 class="card-title text-muted">Başlık Alanı Video</h5>
                                    <Divider class="mb-5"/>
                                    <div class="d-flex flex-column">
                                        <div class="bg-light p-3 mb-3" v-if="form.media_objects.mainVideo">
                                            <video controls class="w-100">
                                                <source :src="form.media_objects.mainVideo.original_url" type="video/mp4">
                                            </video>
                                        </div>
                                        <div class="d-flex flex-row align-items-center">
                                            <PopupMediaLibrary :on-select="setMainVideo" :button-text="'Seç'" :mime-type="'video/'" :key="Math.floor(Math.random() * 100000)"/>
                                            <button type="button" class="btn btn-sm btn-light-danger ms-2" v-if="form.media_objects.mainVideo" @click="form.media_objects.mainVideo = null">Kaldır</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card mb-4 mb-xl-10">
                            <div class="card-body">

                                <h5 class="card-title text-muted">Vergi Sınıfı</h5>
                                <Divider class="mb-10"/>
                                <Dropdown v-model="form.tax_id" :options="props.taxes" showClear optionValue="id" optionLabel="name" placeholder="Seçiniz" class="w-100">
                                    <template #option="slotProps">
                                        <div class="d-flex align-items-center">
                                            <span>{{ slotProps.option.name }}:</span>
                                            <span class="ms-3">{{ slotProps.option.percentage }}%</span>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <div class="card mb-4 mb-xl-10">
                            <div class="card-body">
                                <div class="d-flex flex-row align-items-center">
                                    <InputSwitch v-model="form.users_only" />
                                    <label class="ms-2 fw-bold text-uppercase">ÜYELERE ÖZEL</label>
                                </div>
                                <div class="d-flex flex-row align-items-center mt-4">
                                    <InputSwitch v-model="form.featured" />
                                    <label class="ms-2 fw-bold text-uppercase">ÖNE ÇIKAN ÜRÜN</label>
                                </div>
                                <div class="d-flex flex-row align-items-center mt-4">
                                    <InputSwitch v-model="form.is_published" />
                                    <label class="ms-2 fw-bold text-uppercase">YAYINLA</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">Kaydet</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <ConfirmDialog group="create-combinations"></ConfirmDialog>

</template>

<script setup>

import {ref} from "vue";
import { useForm, usePage } from "@inertiajs/vue3";
import OptionType from "../../Components/OptionType";
import Divider from "primevue/divider";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import InputMask from "primevue/inputmask";
import InputSwitch from "primevue/inputswitch";
import MultiSelect from "primevue/multiselect";
import Dropdown from "primevue/dropdown";
import TextEditor from "../../Components/TextEditor";
import Textarea from "primevue/textarea";
import PopupMediaLibrary from "../MediaLibrary/PopupMediaLibrary";
import AceEditor from "ace-editor-vue3";
import "brace/mode/html";
import "brace/theme/chrome";

import ContentArea from '../Content/DesignElements/ContentArea';

import Accordion from 'primevue/accordion';
import AccordionTab from 'primevue/accordiontab';

import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";

const editor = ref(null);
const optionMediaObjectIndex = ref(null);
const optionMediaObjectRowIndex = ref(null);

const props = defineProps({
    product:Object,
    product_type:Object,
    categories:Array,
    option_group:Object,
    product_attributes:Array,
    taxes:Object,
    language:{
        type:String,
        default:"tr"
    },
    sortables:{
        type:Array,
        default:[]
    },
    layouts:Object
});

const form = useForm({
    sku:props.product.sku,
    language:props.product.language,
    name:props.product.name,
    summary:props.product.summary,
    description:props.product.description,
    additional:{
        footerScripts:props.product.additional && props.product.additional.footerScripts ? props.product.additional.footerScripts : ``,
        headerScripts:props.product.additional && props.product.additional.headerScripts ? props.product.additional.headerScripts :  ``,
        options_for_use:props.product_type.option_group ? (props.product.additional && props.product.additional.options_for_use ? props.product.additional.options_for_use : props.product_type.option_group.options.map(o => o.id)) : []
    },
    product_categories:props.product.categories ? props.product.categories.map((row) => row.id) : [],
    option_group:props.option_group,
    attributes:props.product_attributes,
    price:props.product.price ? props.product.price.price : null,
    has_discount:props.product.has_discount,
    discount_type:props.product.price && props.product.price.discount ? props.product.price.discount.discount_type : "percentage",
    discount_value:props.product.price && props.product.price.discount ? props.product.price.discount.value : 0,
    stock:props.product.stock,
    media_objects:{
        mainImage:props.product.media_objects.mainImage,
        headerImage:props.product.media_objects.headerImage,
        mainVideo:props.product.media_objects.mainVideo,
        galleryImages:props.product.media_objects.galleryImages,
    },
    after_order_download_name:props.product.after_order_download_name,
    after_order_download_file:props.product.after_order_download_file,
    after_order_email_subject:props.product.after_order_email_subject,
    after_order_email_body:props.product.after_order_email_body,
    seo:{
        title:props.product.seo ? props.product.seo.title : "",
        description:props.product.seo ? props.product.seo.description : ""
    },
    use_option_group:props.product.use_option_group,
    use_variables:props.product.use_variables,
    use_extra_costs:props.product.use_extra_costs,
    is_published:props.product.is_published,
    featured:props.product.featured,
    product_layout_id:props.product.product_layout_id,
    content:props.product.content ?? [],
    tax_id:props.product.tax_id,
    users_only:props.product.users_only
});


const discount_types = [
    {
        label:"Yüzde",
        value:"percentage"
    },
    {
        label:"Tutar",
        value:"fixed"
    }
];


const confirm = useConfirm();

const generateOptionCombinations = (attributes) => {

    const generateCombinations = (arr, prefix) => {

        if (arr.length === 0) {
            return [prefix];
        }
        
        let result = [];
        const first = arr[0];
        const rest = arr.slice(1);

        const howMany = first.length === 0 ? 1 : first.length;
        
        for (let i = 0; i < howMany; i++) {
            result = result.concat(generateCombinations(rest, prefix.concat(first[i])));
        }
        
        return result;
    }

    // Extract attribute values into an array. sadece select olanlari alacagiz
    const attributeValues = attributes.filter(attr => form.additional.options_for_use.includes(attr.id) && attr.option_type === 'select').map(attr => attr.option_values);    
    
    // Generate all combinations
    const combinations = generateCombinations(attributeValues, []);

    // Map the combinations back to a more readable format
    return combinations.map(combination => {
        return JSON.parse(JSON.stringify(combination));
    });

}

const addAllCombinationsToForm = () => {

    form.option_group.options = [];

    setTimeout(() => {
        
        const allCombinations = generateOptionCombinations(props.product_type.option_group.options);

        allCombinations.forEach((elem) => {

            const newOption = JSON.parse(JSON.stringify(props.option_group.options[0]));

            newOption.price = form.price;
            newOption.stock = form.stock;

            newOption.row_options.forEach(row => {

                let rowElem = elem.find((x) => x.product_option_group_option_id === row.id);
                if( rowElem ){
                    row.value = rowElem.name;
                }
            
            });

            form.option_group.options.push(JSON.parse(JSON.stringify(newOption)));

        });

    }, 500);

}

const confirmAllCombinations = () => {
    confirm.require({
        group: 'create-combinations',
        message: 'Tüm varyasyonlar yeniden oluşturulacaktır.',
        header: 'Emin misiniz?',
        icon: 'bi bi-info-circle-fill',
        rejectClass: 'p-button-secondary p-button-outlined',
        rejectLabel: 'Vazgeç',
        acceptLabel: 'Evet, oluştur!',
        accept: () => {
            addAllCombinationsToForm()
        },
        reject: () => {
            //
        }
    });
};

const addOptionGroupOptionsRow = (group) => {

    const cloneRow = JSON.parse(JSON.stringify(group.options[0]));
    form.option_group.options.push(cloneRow);
    
    // group.options.push(cloneRow);

    // let cloneRowIndex = group.options.indexOf(cloneRow);

    // group.options[cloneRowIndex].stock = null;
    // group.options[cloneRowIndex].price = null;
    // group.options[cloneRowIndex].row_options.forEach(element => {
    //     element.value = "";
    // });

}

const deleteOptionsRow = (index) => {
    form.option_group.options.splice(index,1);
}

const setMainImage = (media) => {
    form.media_objects.mainImage = media;
}

const setHeaderImage = (media) => {
    form.media_objects.headerImage = media;
}

const setMainVideo = (media) => {
    form.media_objects.mainVideo = media;
}

const setAttributeMedia = (media, attribute) => {
    let attr = form.attributes.find(x => x.id == attribute.id);
    attr.value = media.original_url;
}

const setOptionRowMedia = (media, row) => {
    form.option_group.options[optionMediaObjectRowIndex.value].media_objects.mainImage = media;
}

const setOptionGroupOptionMedia = (media, option) => {
    form.option_group.options[optionMediaObjectRowIndex.value].row_options[optionMediaObjectIndex.value].value = media.original_url;
}

const setGalleryImages = (medias) => {
    form.media_objects.galleryImages = [...form.media_objects.galleryImages, ...medias];
}

const removeFromGallery = (index) => {
    form.media_objects.galleryImages.splice(index,1);
}

const setDownloadableMedia = (media) => {
    form.after_order_download_file = media.original_url;
}

const update = () => {
    form.put(route('products.update', [props.product,{language:props.product.language}]),{
        onSuccess: () => {
            
        },
    });
}

</script>