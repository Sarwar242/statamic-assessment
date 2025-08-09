import Alpine from 'alpinejs';
import { Swiper, Navigation, Pagination } from 'swiper';

// Configure Swiper
Swiper.use([Navigation, Pagination]);

// Alpine.js global data and utilities
Alpine.data('blogOverview', () => ({
    blogs: [],
    categories: [],
    selectedCategory: 'all',
    loading: false,
    currentPage: 1,
    totalPages: 1,
    searchQuery: '',
    
    async init() {
        await this.loadBlogs();
        await this.loadCategories();
    },
    
    async loadBlogs() {
        this.loading = true;
        try {
            let url = `/api/collections/blog?page=${this.currentPage}`;
            if (this.selectedCategory !== 'all') {
                url += `&filter[category]=${this.selectedCategory}`;
            }
            if (this.searchQuery) {
                url += `&filter[title:contains]=${this.searchQuery}`;
            }
            
            const response = await fetch(url);
            const data = await response.json();
            
            if (this.currentPage === 1) {
                this.blogs = data.data;
            } else {
                this.blogs.push(...data.data);
            }
            
            this.totalPages = data.meta.last_page;
        } catch (error) {
            console.error('Error loading blogs:', error);
        } finally {
            this.loading = false;
        }
    },
    
    async loadCategories() {
        try {
            const response = await fetch('/api/taxonomies/categories/terms');
            const data = await response.json();
            this.categories = data.data;
        } catch (error) {
            console.error('Error loading categories:', error);
        }
    },
    
    filterByCategory(category) {
        this.selectedCategory = category;
        this.currentPage = 1;
        this.loadBlogs();
    },
    
    loadMore() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.loadBlogs();
        }
    },
    
    search() {
        this.currentPage = 1;
        this.loadBlogs();
    }
}));

Alpine.data('dynamicCard', () => ({
    activeAccordion: null,
    swiperInstance: null,
    
    init() {
        this.$nextTick(() => {
            this.initSwiper();
        });
    },
    
    initSwiper() {
        const swiperEl = this.$el.querySelector('.swiper');
        if (swiperEl) {
            this.swiperInstance = new Swiper(swiperEl, {
                loop: true,
                autoplay: {
                    delay: 3000,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        }
    },
    
    toggleAccordion(index) {
        this.activeAccordion = this.activeAccordion === index ? null : index;
    }
}));

// Start Alpine
Alpine.start();

// Global utility functions
window.formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};