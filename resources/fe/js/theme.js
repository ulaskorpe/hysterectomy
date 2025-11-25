"use strict";

let openOffcanvasContent;
let initOffcanvasContentSwipers;
let getAvailableWorkingHourSlots;

const ThemeInit = () => {

    if (dynamicOffCanvasDiv) {

        dynamicOffCanvasDiv.addEventListener('hidden.bs.offcanvas', event => {
            dynamicOffCanvasTitle.innerText = '';
            dynamicOffCanvasBody.innerHTML = '<div class="ps-4"><div class="spinner-border spinner-border-lg" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        });
        dynamicOffCanvasDiv.addEventListener('show.bs.offcanvas', event => {
            Fancybox.bind("[data-fancybox]");
        });

    }

    initOffcanvasContentSwipers = () => {

        if(dynamicOffCanvasDiv){
            const contentSwipers = dynamicOffCanvasDiv.querySelectorAll('.ea-swiper');
            Array.from(contentSwipers).forEach(swiper => {
        
                let swiperOptions = JSON.parse(swiper.dataset.swiperOptions);
                let initContentSwiper = new Swiper(swiper, swiperOptions);
        
            });
        }

    }

    openOffcanvasContent = (uuid,language,title) => {

        if( !uuid || !language || !title ){
            return;
        }

        dynamicOffCanvasTitle.innerText = title;
        dynamicOffCanvas.show();

        fetch(`/fetch/offcanvas-content/${uuid}/${language}`).then(function (response) {
            return response.json();
        }).then((json) => {

            setTimeout(() => {
                dynamicOffCanvasBody.innerHTML = json.html;
            },1000);

        }).catch((error) => {
            
            dynamicOffCanvas.hide();

            console.error(error)

        })
    }


    getAvailableWorkingHourSlots = (date,slotsDivId) => {

        const slotsDiv = document.getElementById(slotsDivId);
        slotsDiv.querySelector('.error').classList.add('d-none');
        slotsDiv.querySelector('.slots').innerHTML = '';

        fetch(`/fetch/working-hours/get-available-slots?date=${date}`).then(function (response) {
            return response.json();
        }).then((json) => {

            if( json.slots.length == 0 ){
                slotsDiv.querySelector('.error').classList.remove('d-none');
            } else {


                json.slots.forEach((slot,index) => {

                    let radioDiv = document.createElement('div');
                    let input = document.createElement('input');
                    input.id = slotsDivId+index;
                    input.name = 'appointment_time';
                    input.type = 'radio';
                    input.classList.add('btn-check');
                    input.autocomplete = 'off';
                    input.value = slot.time;
                    input.required = true;
                    if( !slot.available ){
                        input.disabled = true;
                    }
                    radioDiv.appendChild(input);
                    let inputLabel = document.createElement('label');
                    inputLabel.classList.add('btn');
                    inputLabel.classList.add('rounded-pill');
                    inputLabel.classList.add('font-body');
                    inputLabel.setAttribute('for',slotsDivId+index);
                    inputLabel.innerText = slot.time;
                    if( !slot.available ){
                        inputLabel.classList.add("opacity-25");
                    }
                    radioDiv.appendChild(inputLabel);

                    slotsDiv.querySelector('.slots').appendChild(radioDiv);

                });

            }

        }).catch((error) => {
            
            console.error(error)

        });

    }



}

document.addEventListener('DOMContentLoaded', ThemeInit);