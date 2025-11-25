const formatCurrency = num => new Intl.NumberFormat('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(num).replace(/,00$/, '');

var miniChartBody = document.getElementById('mini-cart-body');

const getChartDetails = () => {

    fetch('/cart',{
        headers: {
            'Accept': 'application/json',
        }
    }).then(function (response) {
        return response.json();

    }).then((json) => {

        miniChartBody.innerHTML = json.cartHtml;
        
        const cartItemsBadge = document.getElementById('cart-items-count');
        if(cartItemsBadge){
            cartItemsBadge.classList.remove('opacity-0');
            cartItemsBadge.innerText = json.cartDetails.items_count;
            if( json.cartDetails.items_count === 0 ){
                cartItemsBadge.classList.add('opacity-0');
            }
        }

    });

}

const removeItemFromCart = (itemHash) => {

    FreezeUI({text:' '});

    fetch('/cart/remove/'+itemHash,{
        method: "POST",
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
    }).then(function (response) {
        return response.json();
    }).then((json) => {

        getChartDetails();

        let productCardButtons = document.querySelector(json.productButtons);
        if( productCardButtons ){
            productCardButtons.outerHTML = json.productButtonsHtml;
        }

        UnFreezeUI();

    });

}

const changeItemQuantity = (itemHash,quantity) => {

    FreezeUI({text:' '});
    
    fetch('/cart/change-quantity/'+itemHash+'/'+quantity,{
        method: "POST",
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
    }).then(function (response) {
        return response.json();
    }).then((json) => {
        
        UnFreezeUI();
        getChartDetails();

        let productCardButtons = document.querySelector(json.productButtons);
        if( productCardButtons ){
            productCardButtons.outerHTML = json.productButtonsHtml;
        }

    });

}

const addToCart = (button) => {

    let form = button.closest('form');
    let data = new FormData(form);
    let queryString = new URLSearchParams(data).toString();

    FreezeUI({text:' '});

    fetch('/cart/add-item?'+queryString,{
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
    }).then(function (response) {
        return response.json();
    }).then((json) => {
        
        UnFreezeUI();

        setTimeout(() => {
            
            var swal_html = `<div class="alert alert-success mb-0 px-0" role="alert">
                <div class="container-fluid px-xl-5 px-xxl-7">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success fs-4 me-2"></i>
                        <div>${json.message}</div>
                        <a href="/cart" class="btn btn-success btn-sm ms-auto">Sepete Git</a>
                    </div>
                </div>
            </div>`;

            document.getElementById('flash-message').innerHTML = swal_html;

            let productCardButtons = document.querySelector(json.productButtons);
            if( productCardButtons ){
                productCardButtons.outerHTML = json.productButtonsHtml;
            }

            getChartDetails();

        }, 500);
    });

}


//variant selection
const variantSelection = (product,select = null,queryParams = null) => {

    FreezeUI({ selector: '#product-variant-selection-div', text:' ' });

    let queryString = "";
    let productVariantSelectionDiv = document.getElementById('product-variant-selection-div');

    if( select && queryParams ){
        queryParams[select.dataset.slug] = select.value;

        for (let key in queryParams) {
            if (queryParams.hasOwnProperty(key)) {
                if (queryString.length > 0) {
                    queryString += "&";
                }
                queryString += key + "=" + encodeURIComponent(queryParams[key]);
            }
        }
    }

    fetch('/fetch/variant-selection/' + product.uuid + '?' +queryString,{
        headers:{
            'Accept':'application/json'
        }
    }).then(function(response){
        return response.json();
    }).then((json) => {

        productVariantSelectionDiv.innerHTML = json.html;

    }).then((json) => {
        
        // let variantIdInput = productVariantSelectionDiv.querySelector('[name="variant_id"');

        // if(variantIdInput && variantIdInput.value != "") {
        //     variantPrepareAddToCartButton(variantIdInput.value);
        // } else {

        //     //buna bakicam.
        //     let productVariantButtonsDiv = document.getElementById('product-variant-basket-buttons');

        //     productVariantButtonsDiv.innerHTML = `<div data-bs-toggle="tooltip" title="Lütfen size uygun özellikleri seçiniz.">
        //                                             <div class="d-flex w-100">
        //                                                 <button class="btn btn-secondary rounded-pill w-100 disabled" type="button"><i class="bi bi-basket2 me-2"></i>Sepete Ekle</button>
        //                                                 </div>
        //                                             </div>`;

        // }
        
    })

}

const productOptionSelection = (select = null,queryParams = null) => {

    FreezeUI({ selector: '#product-variant-selection-div', text:' ' });

    let queryString = "";
    let productVariantSelectionDiv = document.getElementById('product-variant-selection-div');

    if( select && queryParams){
        queryParams[select.dataset.slug] = select.value;

        for (let key in queryParams) {
            if (queryParams.hasOwnProperty(key)) {
                if (queryString.length > 0) {
                    queryString += "&";
                }
                queryString += key + "=" + encodeURIComponent(queryParams[key]);
            }
        }
    }

    fetch('/fetch/product-option-selection/' + select.dataset.productUuid + '?' +queryString,{
        headers:{
            'Accept':'application/json'
        }
    }).then(function(response){
        return response.json();
    }).then((json) => {

        productVariantSelectionDiv.innerHTML = json.html;

    }).then((json) => {
        
        // let variantIdInput = productVariantSelectionDiv.querySelector('[name="variant_id"');

        // if(variantIdInput && variantIdInput.value != "") {
        //     variantPrepareAddToCartButton(variantIdInput.value);
        // } else {

        //     //buna bakicam.
        //     let productVariantButtonsDiv = document.getElementById('product-variant-basket-buttons');

        //     productVariantButtonsDiv.innerHTML = `<div data-bs-toggle="tooltip" title="Lütfen size uygun özellikleri seçiniz.">
        //                                             <div class="d-flex w-100">
        //                                                 <button class="btn btn-secondary w-100 disabled" type="button"><i class="bi bi-basket2 me-2"></i>Sepete Ekle</button>
        //                                                 </div>
        //                                             </div>`;

        // }

        initTooltips();
        
    })

}

const variantPrepareAddToCartButton = (variantId) => {

    let productVariantButtonsDiv = document.getElementById('product-variant-basket-buttons');

    fetch('/fetch/variant-selection-buttons/' + variantId,{
        headers:{
            'Accept':'application/json'
        }
    }).then(function(response){
        return response.json();
    }).then((json) => {

        productVariantButtonsDiv.innerHTML = json.html;

    });

}



//Quantity change
const afterQuantityChange = (input) => {

    let form = input.closest('form');
    let minusButton = form.querySelector('[data-type=minus]');
    let plusButton = form.querySelector('[data-type=plus]');
    let minValue =  parseInt(input.getAttribute('min'));
    let maxValue =  parseInt(input.getAttribute('max'));
    let valueCurrent = parseInt(input.value);
        
    if(valueCurrent >= minValue) {
        minusButton.disabled = false;
    } else {
        input.value = maxValue;
    }
    if(valueCurrent <= maxValue) {
        plusButton.disabled = false;
    } else {
        input.value = maxValue;
    }
}
const quantityChange = (button) => {

    let form = button.closest('form');
    let fieldName = button.dataset.field;
    let type = button.dataset.type;
    let input = form.querySelector('input[name="'+fieldName+'"]');
    var currentVal = parseInt(input.value);

    if (!isNaN(currentVal)) {
        if(type == 'minus') {
            
            if(currentVal > input.getAttribute('min')) {
                input.value = currentVal - 1;
                afterQuantityChange(input);
            } 
            if(parseInt(input.value) == input.getAttribute('min')) {
                button.disabled = true;
            }

        } else if(type == 'plus') {

            if(currentVal < input.getAttribute('max')) {
                input.value = currentVal + 1;
                afterQuantityChange(input);
            }
            if(parseInt(input.value) == input.getAttribute('max')) {
                button.disabled = true;
            }

        }
    } else {
        input.value = 1;
    }

}


//Product details
const initProductSwipers = () => {
    const productSwipers = document.querySelectorAll('.product-swiper');

    Array.from(productSwipers).forEach(swiper => {
    
        if( swiper.classList.contains('swiper-initialized') ) return;

        const productSwiper = new Swiper(swiper,{
            pagination: {
                el: ".swiper-pagination",
                clickable:true
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
        });

    });
};

const initRelatedSwipers = () => {

    const relatedSliders = document.querySelectorAll('.related-products-swiper');

    Array.from(relatedSliders).forEach(swiper => {
    
        if( swiper.classList.contains('swiper-initialized') ) return;

        const relatedSwiper = new Swiper(swiper,{
            slidesPerView: 1,
            spaceBetween: 15,
            pagination: {
                el: ".swiper-pagination",
                clickable:true
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 15,
                    },
                992: {
                    slidesPerView: 3,
                    spaceBetween: 15,
                    },
                1200: {
                    slidesPerView: 5,
                    spaceBetween: 15,
                },
            },
        });

    });

}

const initProductMainSwiper = () => {

    const productMainSwiperDiv = document.getElementById('product-main-swiper');
    const productMainThumbSwiperDiv = document.getElementById('product-main-thumb-swiper');

    if(!productMainSwiperDiv) return;

    const productMainThumbSwiper = new Swiper(productMainThumbSwiperDiv,{
        slidesPerView: 5,
        spaceBetween: 15,
        //freeMode: true,
        watchSlidesProgress: true,
        pagination: {
            el: ".swiper-pagination",
            clickable:true,
            dynamicBullets: true,
        },
        breakpoints: {
            992: {
                slidesPerView: 6,
            },
        },
    });

    const productMainSwiper = new Swiper(productMainSwiperDiv,{
        slidesPerView: 1,
        spaceBetween: 0,
        grabCursor:true,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        thumbs: {
            swiper: productMainThumbSwiper,
        },
    });

}

document.addEventListener('DOMContentLoaded', function(){
    initProductSwipers();
    initRelatedSwipers();
    initProductMainSwiper();
});

const openOffCanvasProduct = (event) => {

    if( !event.dataset.productTitle || !event.dataset.productUuid ){
        return;
    }

    dynamicOffCanvasTitle.innerText = event.dataset.productTitle;
    dynamicOffCanvas.show();
    fetch('/fetch/product-details/'+event.dataset.productUuid).then(function(response){
        return response.json();
    }).then((json) => {
        dynamicOffCanvasBody.innerHTML = json.html;
    }).then(() => {

        initRelatedSwipers();
        initOffcanvasContentSwipers();
        initProductSwipers();
        initProductMainSwiper();
        initPopovers();
        initTooltips();

        const formInOffcanvas = dynamicOffCanvasBody.querySelector('.needs-validation');
        const pickadayOffcanvasInputs = dynamicOffCanvasBody.querySelectorAll('.pickaday');

        if(formInOffcanvas){
            checkFormValid(formInOffcanvas);
        }

        Array.from(pickadayOffcanvasInputs).forEach(inputElem => {
            initDatePicker(inputElem);
        });
    })
}

//Adres isleri
const useForBilling = (elem) => {

    const form = elem.closest('form');
    const useForBillingDiv = form.querySelector('.for-billing');
    // const bireyselArea = form.querySelector('.billing-details-bireysel');
    // const kurumsalArea = form.querySelector('.billing-details-kurumsal');

    // bireyselArea.querySelectorAll('input').forEach(input => {
    //     input.removeAttribute('required');
    // });
    // kurumsalArea.querySelectorAll('input').forEach(input => {
    //     input.removeAttribute('required');
    // });

    if(elem.checked) {
        useForBillingDiv.classList.remove("d-none");
        // useForBillingDiv.querySelectorAll('[name="billing_type"]').forEach(input => {
        //     input.setAttribute('required','required');
        // });
        return;
    }

    useForBillingDiv.classList.add("d-none");
    // useForBillingDiv.querySelectorAll('[name="billing_type"]').forEach(input => {
    //     input.removeAttribute('required');
    // });

}

const billingType = (elem) => {

    const form = elem.closest('form');
    const bireyselArea = form.querySelector('.billing-details-bireysel');
    const kurumsalArea = form.querySelector('.billing-details-kurumsal');

    if(elem.value === 'Bireysel') {
        bireyselArea.classList.remove("d-none");
        kurumsalArea.classList.add("d-none");
        bireyselArea.querySelectorAll('input').forEach(input => {
            input.setAttribute('required','required');
        });
        kurumsalArea.querySelectorAll('input').forEach(input => {
            input.removeAttribute('required');
        });
        return;
    }

    bireyselArea.classList.add("d-none");
    kurumsalArea.classList.remove("d-none");
    bireyselArea.querySelectorAll('input').forEach(input => {
        input.removeAttribute('required');
    });
    kurumsalArea.querySelectorAll('input').forEach(input => {
        input.setAttribute('required','required');
    });

}

const addNewAdress = (event) => {

    if( !event.dataset.addressTitle ){
        return;
    }

    dynamicOffCanvasTitle.innerText = event.dataset.addressTitle;
    dynamicOffCanvas.show();

    fetch('/hesabim/adres/create',{
        headers: {
            'Accept': 'application/json',
        }
    }).then(function (response) {
        return response.json();
    }).then((json) => {
        if( json.success ){
            dynamicOffCanvasBody.innerHTML = json.html;
        }
    }).then(()=> {
        checkFormValid(dynamicOffCanvasBody.querySelector('form'));
    })

}

const updateAddress = (event) => {

    if( !event.dataset.addressTitle || !event.dataset.addressId ){
        return;
    }

    dynamicOffCanvasTitle.innerText = event.dataset.addressTitle;
    dynamicOffCanvas.show();

    fetch('/hesabim/adres/'+event.dataset.addressId,{
        headers: {
            'Accept': 'application/json',
        }
    }).then(function (response) {
        return response.json();
    }).then((json) => {
        if( json.success ){
            dynamicOffCanvasBody.innerHTML = json.html;
        }
    });

}


const toggleGiftFriend = (event) => {

    const friendDetailsDiv = document.getElementById('gift-friend-details');
    if(friendDetailsDiv){

        friendDetailsDiv.classList.toggle('d-none');

        if(event.checked) {
            friendDetailsDiv.querySelectorAll('.form-control').forEach(input => {
                input.setAttribute("required","required");
            });
        } else {
            friendDetailsDiv.querySelectorAll('.form-control').forEach(input => {
                input.removeAttribute("required");
            });
        }

    }

}


//bu workshopun ilk zamanlarindan. artik kullanmiyoruz.
const setStockAndButton = (input) => {

    let form = input.closest('form');
    let quantityInput = form.querySelector('[name=quantity]');
    let submitButton = form.querySelector('.btn-add-to-cart');
    let stok = input.dataset.stock;
    
    quantityInput.setAttribute('max',parseInt(stok));
    submitButton.disabled = false;
    
    afterQuantityChange(quantityInput)
}



//sort
const processSorting = (event) => {

    FreezeUI({text:' '});
    const filterForm = document.querySelector('.sticky-filters');
    if(filterForm){
        filterForm.querySelector('[name="sort"]').value = event.value;
        setTimeout(() => {
            filterForm.submit();
        }, 100);
        return;
    }

    event.closest('form').submit()

}



(() => {
    'use strict'
  
    const variantTomSelects = document.querySelectorAll('.tom-select-variant')
    Array.from(variantTomSelects).forEach(select => {
        
        let form = select.closest('form');
        let variants = JSON.parse(select.dataset.variants);
        let options = [];

        Array.from(variants).forEach(variant => {
            
            let option_values = [];

            variant.option_values.forEach(value => {
                options.push({
                    id:variant.id,
                    name:value.name,
                    value:value.value
                });
            });

        });

        new TomSelect(select,{
            valueField: 'id',
            options: variants,
            render: {
                option: function(data, escape) {
                    let optionHtml = '<div class="border-bottom">';
                    optionHtml += '<div class="row g-1">';
                    data.option_values.forEach(element => {
                        optionHtml += '<span class="fw-bold col-4">'+escape(element.name)+'</span><span class="col-8">'+escape(element.value)+'</span>';
                    });
                    optionHtml += '</div>';
                    optionHtml += '</div>';
                    return optionHtml;
                },
                item: function(data, escape) {
                    let itemHtml = '<div class="bg-light rounded p-2">';
                    itemHtml += '<div class="row g-1">';
                    data.option_values.forEach(element => {
                        itemHtml += '<span class="fw-bold col-4">'+escape(element.name)+'</span><span class="col-8">'+escape(element.value)+'</span>';
                    });
                    itemHtml += '</div>';
                    itemHtml += '</div>';
                    return itemHtml;

                },
            },
            onItemAdd:function(value){
                form.querySelector('input[name=variant_id]').value = value;
                form.querySelector('[type=submit]').disabled = false;
            },
            onItemRemove:function(value){
                form.querySelector('input[name=variant_id]').value = "";
                form.querySelector('[type=submit]').disabled = true;
            }
        });

    });

})();



//Kategori menusu

const producMegaLinks = document.querySelectorAll('[data-mega-linkid]');
const producMegaContents = document.querySelectorAll('[data-mega-contentid]');

const productMegaHover = (megaLink) => {

        var megaId = megaLink.dataset.megaLinkid;
        var megaContent = document.querySelector('[data-mega-contentid="'+megaId+'"]');

        Array.from(producMegaLinks).forEach(megaLink => {
            megaLink.classList.remove("active");    
        });

        Array.from(producMegaContents).forEach(megaLink => {
            megaLink.classList.remove("d-flex");
            megaLink.classList.add("d-none");
        });

        megaLink.classList.add("active");
        megaContent.classList.remove("d-none");
        megaContent.classList.add("d-flex");

}