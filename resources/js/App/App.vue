<template>
    <noindex>
        <section class="top-menu">
            <div class="container">
                <div class="top-menu__wrapper">
                    <city-choice class="city-choice"/>
                </div>
            </div>
        </section>

        <header class="fixed-top">
                <div class="navbar">
                    <div class="container">
                        <div class="navbar__wrapper">
                            <div class="header__item header__item-burger" @click="toggleMenu">
                                <div class="burger">
                                    <svg  width="24px" height="24px" viewBox="0 0 24 24">
                                        <path d="M3,11 L21,11 L21,13 L3,13 L3,11 Z M3,6 L21,6 L21,8 L3,8 L3,6 Z M3,16 L21,16 L21,18 L3,18 L3,16 Z"></path>
                                    </svg>
                                </div>
                            </div>


                            <div class="header__item header__logo">
                                <router-link class="header__logo-link" to="/">
                                    <svg class="headerLogo" viewBox="0 0 231 19" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path
                                                d="M11.172 3.804C11.076 3.972 10.972 4.096 10.86 4.176C10.756 4.248 10.624 4.284 10.464 4.284C10.296 4.284 10.112 4.224 9.912 4.104C9.72 3.976 9.488 3.836 9.216 3.684C8.944 3.532 8.624 3.396 8.256 3.276C7.896 3.148 7.468 3.084 6.972 3.084C6.524 3.084 6.132 3.14 5.796 3.252C5.46 3.356 5.176 3.504 4.944 3.696C4.72 3.888 4.552 4.12 4.44 4.392C4.328 4.656 4.272 4.948 4.272 5.268C4.272 5.676 4.384 6.016 4.608 6.288C4.84 6.56 5.144 6.792 5.52 6.984C5.896 7.176 6.324 7.348 6.804 7.5C7.284 7.652 7.776 7.816 8.28 7.992C8.784 8.16 9.276 8.36 9.756 8.592C10.236 8.816 10.664 9.104 11.04 9.456C11.416 9.8 11.716 10.224 11.94 10.728C12.172 11.232 12.288 11.844 12.288 12.564C12.288 13.348 12.152 14.084 11.88 14.772C11.616 15.452 11.224 16.048 10.704 16.56C10.192 17.064 9.564 17.464 8.82 17.76C8.076 18.048 7.224 18.192 6.264 18.192C5.712 18.192 5.168 18.136 4.632 18.024C4.096 17.92 3.58 17.768 3.084 17.568C2.596 17.368 2.136 17.128 1.704 16.848C1.272 16.568 0.888 16.256 0.552 15.912L1.5 14.364C1.58 14.252 1.684 14.16 1.812 14.088C1.94 14.008 2.076 13.968 2.22 13.968C2.42 13.968 2.636 14.052 2.868 14.22C3.1 14.38 3.376 14.56 3.696 14.76C4.016 14.96 4.388 15.144 4.812 15.312C5.244 15.472 5.76 15.552 6.36 15.552C7.28 15.552 7.992 15.336 8.496 14.904C9 14.464 9.252 13.836 9.252 13.02C9.252 12.564 9.136 12.192 8.904 11.904C8.68 11.616 8.38 11.376 8.004 11.184C7.628 10.984 7.2 10.816 6.72 10.68C6.24 10.544 5.752 10.396 5.256 10.236C4.76 10.076 4.272 9.884 3.792 9.66C3.312 9.436 2.884 9.144 2.508 8.784C2.132 8.424 1.828 7.976 1.596 7.44C1.372 6.896 1.26 6.228 1.26 5.436C1.26 4.804 1.384 4.188 1.632 3.588C1.888 2.988 2.256 2.456 2.736 1.992C3.216 1.528 3.808 1.156 4.512 0.875999C5.216 0.596 6.024 0.456 6.936 0.456C7.96 0.456 8.904 0.616 9.768 0.935999C10.632 1.256 11.368 1.704 11.976 2.28L11.172 3.804ZM33.9199 0.647999V18H31.0759V6.792C31.0759 6.344 31.0999 5.86 31.1479 5.34L25.9039 15.192C25.6559 15.664 25.2759 15.9 24.7639 15.9H24.3079C23.7959 15.9 23.4159 15.664 23.1679 15.192L17.8639 5.304C17.8879 5.568 17.9079 5.828 17.9239 6.084C17.9399 6.34 17.9479 6.576 17.9479 6.792V18H15.1039V0.647999H17.5399C17.6839 0.647999 17.8079 0.651999 17.9119 0.659999C18.0159 0.667999 18.1079 0.687999 18.1879 0.719999C18.2759 0.751999 18.3519 0.803999 18.4159 0.875999C18.4879 0.947999 18.5559 1.044 18.6199 1.164L23.8159 10.8C23.9519 11.056 24.0759 11.32 24.1879 11.592C24.3079 11.864 24.4239 12.144 24.5359 12.432C24.6479 12.136 24.7639 11.852 24.8839 11.58C25.0039 11.3 25.1319 11.032 25.2679 10.776L30.3919 1.164C30.4559 1.044 30.5239 0.947999 30.5959 0.875999C30.6679 0.803999 30.7439 0.751999 30.8239 0.719999C30.9119 0.687999 31.0079 0.667999 31.1119 0.659999C31.2159 0.651999 31.3399 0.647999 31.4839 0.647999H33.9199ZM46.9594 11.448L44.8474 5.676C44.7434 5.42 44.6354 5.116 44.5234 4.764C44.4114 4.412 44.2994 4.032 44.1874 3.624C44.0834 4.032 43.9754 4.416 43.8634 4.776C43.7514 5.128 43.6434 5.436 43.5394 5.7L41.4394 11.448H46.9594ZM52.6594 18H50.1634C49.8834 18 49.6554 17.932 49.4794 17.796C49.3034 17.652 49.1714 17.476 49.0834 17.268L47.7874 13.728H40.5994L39.3034 17.268C39.2394 17.452 39.1154 17.62 38.9314 17.772C38.7474 17.924 38.5194 18 38.2474 18H35.7274L42.5554 0.647999H45.8434L52.6594 18ZM59.6994 8.904C60.3074 8.904 60.8354 8.828 61.2834 8.676C61.7394 8.524 62.1114 8.316 62.3994 8.052C62.6954 7.78 62.9154 7.46 63.0594 7.092C63.2034 6.724 63.2754 6.32 63.2754 5.88C63.2754 5 62.9834 4.324 62.3994 3.852C61.8234 3.38 60.9394 3.144 59.7474 3.144H57.6834V8.904H59.6994ZM68.0034 18H65.0874C64.5354 18 64.1354 17.784 63.8874 17.352L60.2394 11.796C60.1034 11.588 59.9514 11.44 59.7834 11.352C59.6234 11.264 59.3834 11.22 59.0634 11.22H57.6834V18H54.4554V0.647999H59.7474C60.9234 0.647999 61.9314 0.771999 62.7714 1.02C63.6194 1.26 64.3114 1.6 64.8474 2.04C65.3914 2.48 65.7914 3.008 66.0474 3.624C66.3034 4.232 66.4314 4.904 66.4314 5.64C66.4314 6.224 66.3434 6.776 66.1674 7.296C65.9994 7.816 65.7514 8.288 65.4234 8.712C65.1034 9.136 64.7034 9.508 64.2234 9.828C63.7514 10.148 63.2114 10.4 62.6034 10.584C62.8114 10.704 63.0034 10.848 63.1794 11.016C63.3554 11.176 63.5154 11.368 63.6594 11.592L68.0034 18ZM81.552 3.3H76.32V18H73.092V3.3H67.86V0.647999H81.552V3.3ZM81.8055 9.516H88.0215V11.988H81.8055V9.516ZM97.8146 15.48C98.4146 15.48 98.9186 15.408 99.3266 15.264C99.7346 15.12 100.059 14.928 100.299 14.688C100.547 14.448 100.723 14.168 100.827 13.848C100.939 13.528 100.995 13.188 100.995 12.828C100.995 12.452 100.935 12.116 100.815 11.82C100.695 11.516 100.507 11.26 100.251 11.052C99.9946 10.836 99.6626 10.672 99.2546 10.56C98.8546 10.448 98.3706 10.392 97.8026 10.392H94.5506V15.48H97.8146ZM94.5506 3.144V8.16H97.1186C98.2226 8.16 99.0546 7.96 99.6146 7.56C100.183 7.16 100.467 6.524 100.467 5.652C100.467 4.748 100.211 4.104 99.6986 3.72C99.1866 3.336 98.3866 3.144 97.2986 3.144H94.5506ZM97.2986 0.647999C98.4346 0.647999 99.4066 0.755999 100.215 0.972C101.023 1.188 101.683 1.496 102.195 1.896C102.715 2.296 103.095 2.78 103.335 3.348C103.575 3.916 103.695 4.556 103.695 5.268C103.695 5.676 103.635 6.068 103.515 6.444C103.395 6.812 103.207 7.16 102.951 7.488C102.703 7.808 102.383 8.1 101.991 8.364C101.607 8.628 101.147 8.852 100.611 9.036C102.995 9.572 104.187 10.86 104.187 12.9C104.187 13.636 104.047 14.316 103.767 14.94C103.487 15.564 103.079 16.104 102.543 16.56C102.007 17.008 101.347 17.36 100.563 17.616C99.7786 17.872 98.8826 18 97.8746 18H91.3226V0.647999H97.2986ZM110.36 3.216V8.028H116.432V10.512H110.36V15.42H118.064V18H107.12V0.647999H118.064V3.216H110.36ZM130.092 11.448L127.98 5.676C127.876 5.42 127.768 5.116 127.656 4.764C127.544 4.412 127.432 4.032 127.32 3.624C127.216 4.032 127.108 4.416 126.996 4.776C126.884 5.128 126.776 5.436 126.672 5.7L124.572 11.448H130.092ZM135.792 18H133.296C133.016 18 132.788 17.932 132.612 17.796C132.436 17.652 132.304 17.476 132.216 17.268L130.92 13.728H123.732L122.436 17.268C122.372 17.452 122.248 17.62 122.064 17.772C121.88 17.924 121.652 18 121.38 18H118.86L125.688 0.647999H128.976L135.792 18ZM144.15 15.396C144.774 15.396 145.33 15.292 145.818 15.084C146.314 14.876 146.73 14.584 147.066 14.208C147.402 13.832 147.658 13.376 147.834 12.84C148.018 12.304 148.11 11.704 148.11 11.04V0.647999H151.338V11.04C151.338 12.072 151.17 13.028 150.834 13.908C150.506 14.78 150.03 15.536 149.406 16.176C148.79 16.808 148.038 17.304 147.15 17.664C146.262 18.016 145.262 18.192 144.15 18.192C143.03 18.192 142.026 18.016 141.138 17.664C140.25 17.304 139.494 16.808 138.87 16.176C138.254 15.536 137.778 14.78 137.442 13.908C137.114 13.028 136.95 12.072 136.95 11.04V0.647999H140.178V11.028C140.178 11.692 140.266 12.292 140.442 12.828C140.626 13.364 140.886 13.824 141.222 14.208C141.566 14.584 141.982 14.876 142.47 15.084C142.966 15.292 143.526 15.396 144.15 15.396ZM167.099 3.3H161.867V18H158.639V3.3H153.407V0.647999H167.099V3.3ZM172.61 18H169.37V0.647999H172.61V18ZM179.735 3.216V8.352H186.239V10.932H179.735V18H176.495V0.647999H187.439V3.216H179.735ZM197.072 15.396C197.696 15.396 198.252 15.292 198.74 15.084C199.236 14.876 199.652 14.584 199.988 14.208C200.324 13.832 200.58 13.376 200.756 12.84C200.94 12.304 201.032 11.704 201.032 11.04V0.647999H204.26V11.04C204.26 12.072 204.092 13.028 203.756 13.908C203.428 14.78 202.952 15.536 202.328 16.176C201.712 16.808 200.96 17.304 200.072 17.664C199.184 18.016 198.184 18.192 197.072 18.192C195.952 18.192 194.948 18.016 194.06 17.664C193.172 17.304 192.416 16.808 191.792 16.176C191.176 15.536 190.7 14.78 190.364 13.908C190.036 13.028 189.872 12.072 189.872 11.04V0.647999H193.1V11.028C193.1 11.692 193.188 12.292 193.364 12.828C193.548 13.364 193.808 13.824 194.144 14.208C194.488 14.584 194.904 14.876 195.392 15.084C195.888 15.292 196.448 15.396 197.072 15.396ZM218.065 15.336V18H207.901V0.647999H211.129V15.336H218.065ZM230.534"
                                                fill="black"/>
                                    </svg>
                                </router-link>
                            </div>


                            <div class="header__item header__search-input">
                                <input placeholder="Поиск товаров" v-model.trim="search" @keyup.enter="searchProduct">

                                <div v-if="search" class="close" @click="clearSearch">
                                    <svg><use xlink:href="#icons_clear">
                                        <symbol viewBox="0 0 24 24" id="icons_clear"><path fill-rule="evenodd" d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm5.412-13.998l-1.414-1.414L12 10.586 8.002 6.588 6.588 8.002 10.586 12l-3.998 3.998 1.414 1.414L12 13.414l3.998 3.998 1.414-1.414L13.414 12l3.998-3.998z"></path></symbol>
                                    </use></svg>
                                </div>

                                <button type="button" class="header__search-button" @click="searchProduct">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24"
                                         class=""
                                         height="24" width="24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10,18c1.846,0,3.543-0.635,4.897-1.688l4.396,4.396l1.414-1.414l-4.396-4.396C17.365,13.543,18,11.846,18,10 c0-4.411-3.589-8-8-8s-8,3.589-8,8S5.589,18,10,18z M10,4c3.309,0,6,2.691,6,6s-2.691,6-6,6s-6-2.691-6-6S6.691,4,10,4z"></path>
                                    </svg>
                                </button>
                            </div>


                            <router-link class="header__item header__item-comparison" :to="{ name: 'comparison' }">
                                     <span v-if="allComparedSkuIdsCount" class="header__item-badge">
                                        {{ allComparedSkuIdsCount }}
                                    </span>
                                <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                     viewBox="0 0 32 32">
                                    <path d="M12 2h20v4h-20v-4zM12 14h20v4h-20v-4zM12 26h20v4h-20v-4zM0 4c0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4s-4-1.791-4-4zM0 16c0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4s-4-1.791-4-4zM0 28c0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.209-1.791 4-4 4s-4-1.791-4-4z"></path>
                                </svg>
                                <span class="header__item-text">Сравнить</span>
                            </router-link>

                            <a class="header__item header__item-favorite" @click="showFavorites">
                                <span v-if="favorites.length" class="header__item-badge">
                                    {{ favorites.length }}
                                </span>
                                <fa class="icon" icon="heart"></fa>
                                <span class="header__item-text">Избранное</span>
                            </a>




                            <dropdown v-if="isAuth" class="header__item header__item-account">
                                <template v-slot:activator="{ on }">
                                    <div class="dropdown__wrapper" @click="on">
                                        <div class="header__item-text auth-button" color="#6c757d">
                                            {{ userName }}
                                        </div>
                                    </div>
                                </template>


                                <router-link class="dropdown__el dropdown__el-username" :to="{ name: 'editProfile'}">
                                    <img class="dropdown__avatar" :src="userAvatar" :alt="userAvatar">
                                    <span class="dropdown__username">{{ userName }}</span>
                                </router-link>
                                <router-link class="dropdown__el" :to="{ name: 'favorites'}">
                                    <svg class="dropdown__icon" viewBox="0 0 24 24">
                                        <path d="M12 4.367C10.675 3.28 9.245 2.72 7.73 2.72A5.73 5.73 0 0 0 2 8.45c0 4.329 3.197 8.531 9.448 12.664l.552.365.551-.365C18.803 16.981 22 12.778 22 8.45a5.73 5.73 0 0 0-5.73-5.73c-1.515 0-2.945.56-4.27 1.648zM4 8.449a3.73 3.73 0 0 1 3.73-3.73c1.223 0 2.402.551 3.566 1.705l.704.698.704-.698c1.164-1.154 2.343-1.704 3.567-1.704A3.73 3.73 0 0 1 20 8.45c0 3.397-2.623 6.956-8 10.626-5.377-3.67-8-7.23-8-10.627z"></path>
                                    </svg>
                                    <span>Избранное</span>
                                    <span class="dropdown__count" v-if="favorites.length">{{ favorites.length }}</span>
                                </router-link>
                                <router-link class="dropdown__el" :to="{ name: 'comparison'}">
                                    <svg class="dropdown__icon" viewBox="0 0 24 24">
                                        <path d="M3 7V4h2v3l3 .001v2H5V12H3V9H0V7h3zm5 6.004v-2h13v2H8zM10 7v2h11V7H10zM3 17.002v-2h18v2H3zM3 19v2h18v-2H3z"></path>
                                    </svg>
                                    <span>Списки сравнения</span>
                                    <span class="dropdown__count" v-if="allComparedSkuIdsCount">{{ allComparedSkuIdsCount }}</span>
                                </router-link>
                                <router-link class="dropdown__el" :to="{ name: 'myReviews'}">
                                    <svg class="dropdown__icon" viewBox="0 0 24 24">
                                        <path d="M4.4 23h12.2a1.4 1.4 0 0 0 1.4-1.4V19h2.6a1.4 1.4 0 0 0 1.4-1.4V2.4A1.4 1.4 0 0 0 20.6 1H8.4A1.4 1.4 0 0 0 7 2.4V5H4.4A1.4 1.4 0 0 0 3 6.4v15.2A1.4 1.4 0 0 0 4.4 23zM5 7h11v14H5V7zm4-4h11v14h-2V6.4A1.4 1.4 0 0 0 16.6 5H9V3z"></path>
                                        <path d="M14 9H7v2h7V9zM14 13H7v2h7v-2z"></path>
                                    </svg>
                                    <span>Мои публикации</span>
                                </router-link>
                                <div class="dropdown__el" @click="exit">
                                    <svg class="dropdown__icon" viewBox="0 0 24 24">
                                        <path d="M7 11h8.987v2H7v2.964l-4-4 4-4V11zm2-6V3h7a5 5 0 0 1 5 5v8a5 5 0 0 1-5 5H9v-2h7a3 3 0 0 0 3-3V8a3 3 0 0 0-3-3H9z"
                                              fill="#222"></path>
                                    </svg>
                                    <span>Выйти</span>
                                </div>
                            </dropdown>


                            <div v-else class="dropdown__wrapper header__item-account" @click="setIsShowAuthModal(true)">
                                <div class="header__item-text auth-button">
                                    Войти
                                </div>
                            </div>
                        </div>
                        <div class="search">
                            <div class="search__wrapper">
                                <input class="search__input" placeholder="Поиск товаров" v-model.trim="search"
                                       @keyup.enter="searchProduct">
                                <button type="button" class="header__search-button"
                                        @click="searchProduct">
                                    <svg stroke="currentColor" fill="currentColor" stroke-width="0"
                                         viewBox="0 0 24 24" class="" height="24" width="24"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10,18c1.846,0,3.543-0.635,4.897-1.688l4.396,4.396l1.414-1.414l-4.396-4.396C17.365,13.543,18,11.846,18,10 c0-4.411-3.589-8-8-8s-8,3.589-8,8S5.589,18,10,18z M10,4c3.309,0,6,2.691,6,6s-2.691,6-6,6s-6-2.691-6-6S6.691,4,10,4z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <menu-list
                        v-model:isShowMobileMenu="isShowMobileMenu"
                        class="main-menu"
                />

        </header>
    </noindex>

    <main class="main">
        <div class="container">
            <router-view></router-view>
        </div>
    </main>

    <footerBlock/>

    <auth-wrapper/>
    <div  v-if="isShowScrollUp" class="scroll-up" @click="scrollTop">
        <fa icon="arrow-up"></fa>
    </div>
<!--    <bottom-bar></bottom-bar>-->
    <top-notification>
        <comparison-notification/>
    </top-notification>
    <notification/>
</template>

<script setup>
    import notification from  './components/notification.vue';
    import comparisonNotification from  './components/comparison-notification';
    import topNotification from  './components/top-notification';
    import dropdown from './components/dropdown';
    import authWrapper from './components/auth';
    import menuList from './components/menu';
    import cityChoice from './components/city';
    import footerBlock from './components/footer';

    import { onMounted,  watch, ref } from 'vue';
    import { storeToRefs } from "pinia";
    import {useAuthStore} from './store/auth';
    import {useFavoritesStore} from './store/favorites';
    import {useComparisonStore} from './store/comparison';
    import {useFilterStore} from './store/filter';
    import {useRoute, useRouter} from "vue-router";


    let isShowDropdown = ref(false);
    let isShowScrollUp = ref(false);
    let isShowMobileMenu = ref (false);
    let search = ref('');

    const route = useRoute();
    const router = useRouter();

    const authStore = useAuthStore();
    const favoritesStore = useFavoritesStore();
    const comparisonStore = useComparisonStore();
    const filterStore = useFilterStore();
    const { isAuth, userName, userAvatar } = storeToRefs(authStore);
    const { favorites } = storeToRefs(favoritesStore);
    const { allComparedSkuIdsCount } = storeToRefs(comparisonStore);

    const setIsShowAuthModal = authStore.setIsShowAuthModal;

    const scrollTop = () => window.scrollTo(0, 0);
    const showFavorites = () => {
        if (!isAuth.value) {
            setIsShowAuthModal(true);
        } else {
            router.push({  name: 'favorites' });
        }
    };
    const toggleMenu = () => isShowMobileMenu.value = !isShowMobileMenu.value;
    const showDropdown = () => isShowDropdown.value = true;

    const exit = () => {
        router.push('/');
        authStore.logout();
        isShowDropdown.value = false;
    };

    const clearSearch = () => search.value = '';

    const searchProduct = () => router.push({ name: 'search', query: { search: search.value }});


    watch(isAuth, () => {
        favoritesStore.getFavoriteSkuIds()
    });

    watch(route.query.search, (value) => {
        if (![undefined, null].includes(value)) {
            search.value = value;
        } else {
            search.value = '';
        }
    });


    onMounted(() => {
        window.onscroll = () => {
            isShowScrollUp.value = window.scrollY > 33;

            const productList = document.getElementById('productList');
            if (productList) {
                filterStore.setProductDom(productList);
            }
        }

        authStore.checkAuth();
        comparisonStore.checkCompared();
    });
</script>

<style lang="scss" scoped>

    a {
        text-decoration: none;
        color: #333;
    }

    .top-menu {
        z-index:100;
        color: gray;
        height: 33px;
        //background-color: #ffedea;
        background-color: #ffeaf2;
        /*border-bottom: 1px solid #c4c4c4;*/
        &__wrapper {
            height:100%;
            display:flex;
            align-items:center
        }
    }
    .city-choice {
        cursor: pointer;
    }

    .navbar {
        height: 80px;
        background-color: #fff;
        border-bottom: 1px solid #333;
        &__wrapper {
            height: 100%;
            display: flex;
            justify-content: space-between;
            align-items:center;
        }
    }
    .fixed-top {
        z-index:80;
        box-shadow: 0 9px 10px 0 rgba(0,0,0,0.14);
        border-top-color: rgba(0,0,0,0);
        border-bottom-color: rgba(0,0,0,0);
    }
    .header {
        &__item {
            white-space: nowrap;
            position: relative;
            color: #333;
            cursor:pointer;
            display:flex;
            align-items:center;
            &:not(:last-child) {
                margin-right: 18px;
            }
            &-burger {
                display: none;
            }

            &-text {
                margin-left: 10px;
            }
            &-badge {
                position: absolute;
                left: 9px;
                top: -7px;
                height: 20px;
                line-height: 20px;
                min-width: 20px;
                padding: 0 5px;
                display: inline-flex;
                justify-content: center;
                align-items: center;
                border-radius: 50%;
                background-image: linear-gradient(to bottom, #ffd6e7, rgb(248, 140, 182));
                color: #fff;
                font-size: 12px;
                font-weight: bolder;
                text-align: center;
            }
        }
        &__logo {
            box-sizing: border-box;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-basis: 200px;
            flex-grow: 0;
            min-height:40px;
            &-link {
                height:100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            &  .headerLogo {
                max-width: 200px;
                height: 20px;
            }
        }
        &__search {
            &-input {
                position: relative;
                height: 38px;
                white-space: nowrap;
                display: inline-block;
                flex:2 2 550px;
                overflow:hidden;
                box-sizing: content-box;
                border-radius: 8px;
                border: 1px solid #d9d9d9;

                & > input  {
                    outline: #000 none medium;
                    box-sizing: border-box;
                    background-color: transparent;
                    -webkit-appearance: textfield;
                    border: none;
                    line-height: 24px;
                    padding: 0 76px 0 10px;
                    font-size: 14px;
                    width: 100%;
                    height: 100%;
                    &::placeholder {
                        color: rgb(0, 0, 0);
                    }
                }
            }

            &-button {
                background-color: #ffeaf2;
                border: none;
                position: absolute;
                top: 0;
                right: 0;
                width: 46px;
                height: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            &-mobile {
                display: none;
            }
        }
    }


    .icon {
        color: #222;
        &__comparison {
            padding-left: 20px;
            background-image: url(data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTEzLjMzMyAxNC4xNjdWMTIuNUgxNXYxLjY2N2gxLjY2N3YxLjY2NkgxNVYxNy41aC0xLjY2N3YtMS42NjdoLTEuNjY2di0xLjY2NmgxLjY2NnptLTEwIDBIMTB2MS42NjZIMy4zMzN2LTEuNjY2em0wLTEwaDEzLjMzNHYxLjY2NkgzLjMzM1Y0LjE2N3ptMCA1aDEzLjMzNHYxLjY2NkgzLjMzM1Y5LjE2N3oiIGZpbGw9IiMwNEIiIGZpbGwtcnVsZT0iZXZlbm9kZCIvPjwvc3ZnPg==);
        }
    }

    .auth-button {
        color: #333;
        height: 38px;
        position: relative;
        min-width:74px;
        font-size: 16px;
        text-align: center;
        line-height: 36px;
        border-radius: .5rem;
        border: 1px solid #d9d9d9;
        cursor: pointer;
        background-color: #e8e8e8;
        transition: all .12s ease-out;

        &:hover {
            background-color: #dcdcdc;
        }
    }

    .dropdown {
        &__wrapper {
            display: block;
        }

        &__el {
            position: relative;
            display: flex;
            align-items: center;
            width:100%;
            padding: 8px 0 8px 30px;
            min-height:40px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            line-height: 1.5;
            letter-spacing: 0;
            color: #333;
            font-weight: 400;
            box-sizing: border-box;
            text-decoration: none;
            &:hover {
                background-color: #f5f5f5;
            }
            &-username {
                padding: 16px 8px 16px 16px;
            }
        }
        &__avatar {
            overflow: hidden;
            border-radius: 50%;
            height: 40px;
            width: 40px;
            margin-right: 16px;
        }
        &__username {
            font-weight: 700;
            line-height: 24px;
        }
        &__icon {
            fill: #222;
            margin-right:12px;
            width: 24px;
            height: 24px;
        }
        &__count {
            color: #999;
            display: inline-block;
            margin-left: 10px;
        }
    }

    .close {
        position: absolute;
        top: 0;
        right: 46px;
        width: 30px;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        & svg {
            height: 16px;
            width: 16px;
        }
    }
    .search {
        display: none;
    }


    .main {
        flex: 1 1 auto;
        margin-bottom: 30px;
    }



    .scroll-up {
        cursor: pointer;
        border: 1px solid #d0d0d0;
        border-radius: 7px;
        z-index: 10;
        position: fixed;
        right: 20px;
        bottom: 80px;
        width: 48px;
        height: 48px;
        padding: 11px 0 0;
        text-align: center;
        color: #8c8c8c;;
        box-shadow: 0 0 14px rgba(0, 0, 0, .25);
        background: #fff;
        opacity: .9;

      /*  &:before {
            content: '';
            position: absolute;
            height: 32px;
            width: 32px;
            top: 10px;
            left: 7px;
            background: #fff url(/img/arrows-v3-1.png) no-repeat 0 -407px;
        }*/
    }


    @media (max-width: 900px) {
        .navbar {
            height: auto;
            border: none;
            &__wrapper {
                height: 44px;
            }
        }
        .header {
            &__logo {

                box-sizing: border-box;
                display: flex;
                justify-content: center;
                align-items: center;
                padding-left: 8px;
                padding-right: 8px;
                flex-basis: 104px;
                flex-grow: 0;
                &-link {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    & .headerLogo {
                        max-width: 200px;
                        height: 18px;
                    }
                }
            }
            &__search {
                &-input {
                    display: none;
                }
                &-mobile {
                    display: flex;
                    border:none;
                    background-color: transparent;
                    appearance: none;
                    cursor: pointer;
                    align-items: center;
                    justify-content: center;
                    height: 40px;
                    padding-left: 8px;
                    padding-right: 8px;
                    margin-left: auto;
                    margin-right: auto;
                    z-index: 3;
                    overflow: hidden;
                    order: 2;
                    flex-basis: 48px;
                }
            }

            &__item {
                &-text,
                &-comparison,
                &-notification,
                &-account {
                    display: none;
                }
                &-badge {
                    height: 16px;
                    line-height: 16px;
                    min-width: 16px;
                }

                &-burger {
                    display:block;
                    height:100%;
                    margin-left: -8px;
                    & .burger {
                        height: 100%;
                        margin: 0 14px 0 8px;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                    }
                }
            }
        }

        .search {
            padding: 0 0 12px;
            display: flex;
            height: 100%;
            align-items: center;

            &__wrapper {
                position: relative;
                box-sizing: border-box;
                white-space: nowrap;
                display: inline-block;
                border: 1px solid #c1c1c1;
                border-radius: 8px;
                overflow: hidden;
                width: 100%;
                &:hover {
                    border-color: rgb(192, 201, 240);
                    transition: border-color 0.3s ease 0s;
                }
            }

            &__input {
                border: none;
                outline: #000 none medium;
                overflow: visible;
                transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
                padding: 8px;
                width: 100%;
                &:focus {
                    background-color: white;
                    border-color: rgb(59, 87, 208);
                    transition: background-color 0.3s ease 0s, border-color 0.3s ease 0s;
                }
            }
        }


        .top-menu,
        .scroll-up {
            display: none;
        }
    }


    @media (max-width: 500px)   {
        .header {
            &__logo {
                &-link {
                    & .headerLogo {
                        height: 15px;
                    }
                }
            }
        }
    }
    @media (max-width: 400px)   {
        .header {
            &__logo {
                &-link {
                    & .headerLogo {
                        height: 13px;
                    }
                }
            }
        }
    }
</style>
