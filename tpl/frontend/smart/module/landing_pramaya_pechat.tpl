<{include file='core/breadcrumb.tpl' arrBreadCrumb=$arrPageData.arrBreadCrumb}>
<div class="top-banner landing pramaya-pechat">
    <div class="container">
        <div class="anchor-links">
            <ul>
                <li>
                    <a href="#technology">Технология нанесения</a>
                </li>
                <li>
                    <a href="#price-table">Цены</a>
                </li>
                <li>
                    <a href="#t-shirt">Одежда</a>
                </li>
                <li>
                    <a href="#our-works">Примеры работ</a>
                </li>
            </ul>
        </div>
        <h1 class="heading">Прямая печать <br>на одежде и аксессуарах</h1>
        <p class="sub-heading">Мы просчитаем стоимость заказа для Вас в течение одного часа</p>
        <button class="btn btn-warning" onclick="Modal.open('<{include file="core/href.tpl" arCategory=$arrModules.request}>');">Узнать цены</button>
    </div>
</div>
         
<div class="technology-print" id="technology">
    <div class="inner middle-container container">
        <div class="signature">
            <h2>
                Технология прямой печати
            </h2>
            <p>
                Прямая печать – технология нанесения рисунка или надписей сразу на текстиль, без использования
                промежуточных носителей изображения. Выполняется при помощи текстильного принтера, что по принципу
                работы напоминает струйный принтер. Этим методом можно изготовить любое количество изделий
                с принтом, притом для цифровой фотопечати подходят изделия, которые содержат не менее 80% хлопка.
            </p>
        </div>
        <div class="technology-print-wrapper">
            <div class="print-method">
                <img src="/images/tmp/landings/pramaya-pechat/1.jpg" alt="">
                <div>
                    <h3>Полноцветная печать любой сложности</h3>
                    <p>
                        Основное достоинство прямой цифровой фотопечати – возможность нанести на ткань полноцветное изображение любой 
                        сложности с высоким уровнем детализации. Есть возможность реализовывать сложные спецэффекты вроде плавных градиентов.
                    </p>
                </div>
            </div>
            <div class="print-method">
                <img src="/images/tmp/landings/pramaya-pechat/2.jpg" alt="">
                <div>
                    <h3>Яркие цвета, экологически безопасные краски</h3>
                    <p>
                        Для нанесения изображения или надписи используются экологически чистые краски на водной основе. 
                        Цифровая печать на текстиле позволяет использовать все возможные цвета для создания уникального принта.
                    </p>
                </div>
            </div>
            <div class="print-method">
                <img src="/images/tmp/landings/pramaya-pechat/3.jpg" alt="">
                <div>
                    <h3>Прямая печать на ткани в Киеве</h3>
                    <p>
                        Пришлите нам свой эскиз, и мы перенесём его на выбранную вами одежду. Прямая цифровая печать на изделиях 
                        из трикотажа занимает всего 15 минут. Применяя этот метод, можно изготовить как 1 авторскую футболку для 
                        себя или на подарок, так и оптовую партию для корпоративных клиентов или на продажу.
                    </p>
                </div>
            </div>
            <div class="print-method">
                <img src="/images/tmp/landings/pramaya-pechat/4.jpg" alt="">
                <div>
                    <h3>Гарантия на изделия с принтом</h3>
                    <p>
                        Благодаря идеальной впитываемости краски в текстиль, изделия с рисунком, нанесённым методом 
                        прямой печати служат очень долго. Они выдерживают около 50 стирок в машинке – автомат и 
                        устойчивы к механическому воздействию. Изображения можно даже гладить утюгом без риска 
                        испортить изделие.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<{*<div class="price-table" id="price-table">
    <div class="inner middle-container container">
        <div class="signature">
            <h2>
                <span>Цены</span> на прямуюю печать</h2>
            <p>При заказе от 10 шт скидка -10% для всех видов печати<br>
            При заказе более 20 шт стоимость печати рассчитывается индивидуально</p>
            <div class="calc icon"></div>
        </div>
        <div class="price-table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th colspan="3">Прямая печать (цифровая печать) от 1 шт</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Формат</th>
                        <th>На светлой одежде</th>
                        <th>На цветной одежде</th>
                    </tr>
                    <tr>
                        <td>A6 (100*100мм)</td>
                        <td>70</td>
                        <td>115</td>
                    </tr>
                    <tr>
                        <td>A5 (148*210мм)</td>
                        <td>90</td>
                        <td>180</td>
                    </tr>
                    <tr>
                        <td>A4 (210*297мм)</td>
                        <td>115</td>
                        <td>215</td>
                    </tr>
                    <tr>
                        <td>A3 (297*420мм)</td>
                        <td>160</td>
                        <td>275</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5">
                            <p>Цены указаны на цифровую печать на одной футболке без учета стоимости
                                самой футболки в грн.
                            </p>
                            <p>
                                <span class="bold">Срочная печать</span> все виды +20-50% к стоимости
                            </p>
                            <p>
                                <span class="bold">Стандартная печать</span> от 2-х рабочих дней 
                                (сроки производства на момент заказа уточняйте у менеджера).
                            </p>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="message"> 
            <h3>Обратитесь к нам для точного расчета стоимости </h3> 
            <button class="btn btn-warning" onclick="Modal.open('<{include file="core/href.tpl" arCategory=$arrModules.request}>');">Отправить заявку на расчет</button>
        </div>
    </div>
</div>*}>
            
<div class="section-features">
    <div class="container">
        <span class="sm-sign">Превратите свои лучшие моменты в прекрасные воспоминания</span>
        <h2 class="signature">Печатайте<span> логотипы и надписи</span></h2>
        <div class="features express">
            <div class="features-item">
                <div class="features-info icon"></div>
                <h4>Экспресс печать</h4>
                <p class="text-info">
                     Срочная печать в течении 24 часов 
                    (возможность осуществить заказ в течении 
                    24 часов необходимо уточнить у менеджера)
                </p>
            </div>
            <div class="features-item">
                <div class="features-info icon"></div>
                <h4>Печать на разных изделиях</h4>
                <p class="text-info">
                    Печать на футболках, толстовках, жилетках, зонтах, сумках, чехлах и куртках
                </p>
            </div>
            <div class="features-item">
                <div class="features-info icon card"></div>
                <h4>Безналичный расчет</h4>
                <p class="text-info">
                    Используйте наиболее удобный вид оплаты при оформлении заказа
                </p>
            </div>
            <div class="features-item">
                <div class="features-info icon"></div>
                <h4>Бесплатная доставка</h4>
                <p class="text-info">
                    При заказе от 4-х изделий с печатью (в регионы согласно тарифов Новой Почты)
                </p>
            </div>
        </div>
    </div>
</div>  
            
<div class="t-shirt-type" id="t-shirt">
    <div class="container">
        <h3>Выберите тип одежды</h3>
        <div class="flex">
            <ul class="tabs">
                <li class="tab active" data-index="0">
                    Мужская
                </li>
                <li class="tab" data-index="1">
                    Женская
                </li>
                <li class="tab" data-index="2">
                    Детская
                </li>
            </ul>
        </div>
    </div>
    <div class="tab_content">
        <div class="tab-item product-slider-2 product-swiper-element">
            <div class="arrows">
                <div class="swiper-button-prev btn-pv swiper-button-prev-0"></div>
                <div class="swiper-button-next btn-nx swiper-button-next-0"></div>
            </div>
            <div class="swiper-container swiper-container-horizontal">
                <div class="swiper-scrollbar">
                    <div class="swiper-scrollbar-drag"></div>
                </div>
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_110.html">
                                <img src="/images/tmp/landings/pechat-logo/man/1.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_110.html">
                                    Футболки
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 70</strong> грн
                            </p>
                        </div>
                    </div>
                   <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_111.html">
                                <img src="/images/tmp/landings/pechat-logo/man/2.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_111.html">
                                    Футболки поло
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 170</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_112.html">
                                <img src="/images/tmp/landings/pechat-logo/man/3.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_112.html">
                                    Футболки с длинным рукавом
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 140</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_163.html">
                                <img src="/images/tmp/landings/pechat-logo/man/4.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_163.html">
                                    Футболки поло с длинным рукавом
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 210</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_115.html">
                                <img src="/images/tmp/landings/pechat-logo/man/10.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_115.html">
                                    Свитшоты
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 235</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_113.html">
                                <img src="/images/tmp/landings/pechat-logo/man/9.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_113.html">
                                    Толстовки
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 295</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_148.html">
                                <img src="/images/tmp/landings/pechat-logo/man/8.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_148.html">
                                    Флисовые кофты
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 370</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_149.html">
                                <img src="/images/tmp/landings/pechat-logo/man/7.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_149.html">
                                    Ветровки
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 290</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_148.html">
                                <img src="/images/tmp/landings/pechat-logo/man/6.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_148.html">
                                    Жилетки
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 650</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_114.html">
                                <img src="/images/tmp/landings/pechat-logo/man/5.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_114.html">
                                    Майки
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 170</strong> грн
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-item product-slider-2 product-swiper-element">
            <div class="arrows hidden">
                <div class="swiper-button-prev btn-pv swiper-button-prev-0"></div>
                <div class="swiper-button-next btn-nx swiper-button-next-0"></div>
            </div>
            <div class="swiper-container swiper-container-horizontal hidden">
                <div class="swiper-scrollbar">
                    <div class="swiper-scrollbar-drag"></div>
                </div>
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_117.html">
                                <img src="/images/tmp/landings/pechat-logo/woman/2.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_117.html">
                                    Футболки
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 70</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_119.html">
                                <img src="/images/tmp/landings/pechat-logo/woman/3.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_119.html">
                                    Футболки поло
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 170</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_118.html">
                                <img src="/images/tmp/landings/pechat-logo/woman/5.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_118.html">
                                    Футболки с длинным рукавом
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 140</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_164.html">
                                <img src="/images/tmp/landings/pechat-logo/woman/6.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_164.html">
                                    Футболки поло с длинным рукавом
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 210</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="">
                                <img src="/images/tmp/landings/pechat-logo/woman/8.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="">
                                    Свитшоты
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 235</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_134.html">
                                <img src="/images/tmp/landings/pechat-logo/man/9.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_134.html">
                                    Толстовки
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 295</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_151.html">
                                <img src="/images/tmp/landings/pechat-logo/woman/9.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_151.html">
                                    Флисовые кофты
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 370</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_151.html">
                                <img src="/images/tmp/landings/pechat-logo/man/6.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_151.html">
                                    Жилетки
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 650</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/category_116.html">
                                <img src="/images/tmp/landings/pechat-logo/woman/7.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/category_116.html">
                                    Майки
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 170</strong> грн
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-item product-slider-2 product-swiper-element">
            <div class="arrows hidden">
                <div class="swiper-button-prev btn-pv swiper-button-prev-0"></div>
                <div class="swiper-button-next btn-nx swiper-button-next-0"></div>
            </div>
            <div class="swiper-container swiper-container-horizontal hidden">
                <div class="swiper-scrollbar">
                    <div class="swiper-scrollbar-drag"></div>
                </div>
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/futbolki-170126165510.html">
                                <img src="/images/tmp/landings/pechat-logo/kids/1.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/futbolki-170126165510.html">
                                    Футболки
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 75</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/futbolki-polo-170126165510.html">
                                <img src="/images/tmp/landings/pechat-logo/kids/4.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/futbolki-polo-170126165510.html">
                                    Футболки поло
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 240</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/tolstovki-170126165510.html">
                                <img src="/images/tmp/landings/pechat-logo/kids/3.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/tolstovki-170126165510.html">
                                    Свитшоты
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 235</strong> грн
                            </p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="product-item">
                            <a href="/tolstovki-170126165510.html">
                                <img src="/images/tmp/landings/pechat-logo/kids/2.jpg" alt="">
                            </a>
                            <p class="product-name-big">
                                <a href="/tolstovki-170126165510.html">
                                    Толстовки
                                </a>
                            </p>
                            <p class="product-price">
                                от<strong> 285</strong> грн
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <{include file="core/callback-form-inline.tpl"}>
</div>
 
<div class="brand-slider no-image" id="our-works">
    <h2>Примеры работ</h2>
    <span>Интересными идеями и нестандартными решениями мы делимся с вами.
    <br>Надеемся, примеры работ вдохновят на создание уникального образа для вас и ваших сотрудников.</span>
    <div class="brand-swiper-element">
        <div class="arrows">
            <div class="swiper-button-prev btn-pv swiper-button-prev-0" tabindex="0" role="button" aria-label="Previous slide" aria-disabled="false"></div>
            <div class="swiper-button-next btn-nx swiper-button-next-0" tabindex="0" role="button" aria-label="Next slide" aria-disabled="false"></div>
        </div>
        <div class="swiper-container brand-wrapper swiper-container-horizontal swiper-container-free-mode">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="product-item">
                        <img src="/images/tmp/landings/pramaya-pechat/brand-slider/1.jpg" alt="">
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="product-item">
                        <img src="/images/tmp/landings/pramaya-pechat/brand-slider/2.jpg" alt="">
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="product-item">
                        <img src="/images/tmp/landings/pramaya-pechat/brand-slider/3.jpg" alt="">
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="product-item">
                        <img src="/images/tmp/landings/pramaya-pechat/brand-slider/4.jpg" alt="">
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="product-item">
                        <img src="/images/tmp/landings/pramaya-pechat/brand-slider/5.jpg" alt="">
                    </div>
                </div>
                
                <div class="swiper-slide">
                    <div class="product-item">
                        <img src="/images/tmp/landings/pramaya-pechat/brand-slider/6.jpg" alt="">
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="product-item">
                        <img src="/images/tmp/landings/pramaya-pechat/brand-slider/7.jpg" alt="">
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="product-item">
                        <img src="/images/tmp/landings/pramaya-pechat/brand-slider/8.jpg" alt="">
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="product-item">
                        <img src="/images/tmp/landings/pramaya-pechat/brand-slider/9.jpg" alt="">
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="product-item">
                        <img src="/images/tmp/landings/pramaya-pechat/brand-slider/10.jpg" alt="">
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="product-item">
                        <img src="/images/tmp/landings/pramaya-pechat/brand-slider/11.jpg" alt="">
                    </div>
                </div>
            </div>
            <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
    </div>
</div>
            
<div class="section-under-features landing">
    <div class="under-features">
        <ul>
            <li>
                <span></span>
                <h3 title="">Выберите тип товара <br>для печати</h3>
                <p>
                    Выберите цвет и тип товара, производитель и количество 
                </p>
            </li>
            <li>
                <span></span>
                <h3 title="">Выбор товара с принтом <br>либо загрузите свой лого</h3>
                <p>
                    У вас есть возможность выбрать товар с принтом из наших коллекций либо отправить свой лого для печати
                </p>
            </li>
            <li>
                <span></span>
                <h3 title="">Оформить заказ <br>в 2 клика</h3>
                <p>
                    На странице оформления заказа укажите свой номер телефона
                </p>
            </li>
            <li>
                <span></span>
                <h3 title="">Подтверждение <br>по телефону</h3>
                <p>
                    В ближайшее время с вами свяжется менеджер по телефону, для подтверждения заказа
                </p>
            </li>
        </ul>
    </div>
</div>

<{include file="core/feedback-form-inline.tpl"}>
<{include file='core/seo-text.tpl'}>