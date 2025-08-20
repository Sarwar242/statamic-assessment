import Alpine from 'alpinejs';
import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';

// Configure Swiper
Swiper.use([Navigation, Pagination]);

// Alpine.js global data and utilities
Alpine.data('blogOverview', () => ({
    searchQuery: '',
    selectedCategory: 'all',
    blogs: [],
    categories: [],
    loading: true,
    loadingMore: false,
    currentPage: 1,
    totalPages: 1,
    hasMore: false,

    async init() {
        await this.loadBlogs();
        await this.loadCategories();
        this.loading = false;
    },

    async loadBlogs() {
        try {
            let url = `/api/collections/blog?page=${this.currentPage}`;
            if (this.selectedCategory !== 'all') {
                url += `&filter[categories:contains]=${this.selectedCategory}`;
            }
            if (this.searchQuery) {
                url += `&filter[title:contains]=${this.searchQuery}`;
            }
            
            console.log('Fetching from URL:', url);
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            console.log('API Response:', data);
            
            if (this.currentPage === 1) {
                this.blogs = data.data || [];
            } else {
                this.blogs.push(...(data.data || []));
            }
            
            console.log('Blogs array after update:', this.blogs);
            console.log('Filtered blogs:', this.getFilteredBlogs());
            
            this.totalPages = data.meta?.last_page || 1;
            this.hasMore = this.currentPage < this.totalPages;
        } catch (error) {
            console.error('Error loading blogs:', error);
            // Fallback to static data if API fails
            this.loadFallbackData();
        }
    },

    async loadCategories() {
        try {
            const response = await fetch('/api/taxonomies/categories/terms');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            this.categories = data.data || [];
        } catch (error) {
            console.error('Error loading categories:', error);
            // Fallback to static categories
            this.categories = [
                { id: 1, title: 'Sustainability', slug: 'sustainability' },
                { id: 2, title: 'Technology', slug: 'technology' },
                { id: 3, title: 'Installation', slug: 'installation' }
            ];
        }
    },

    loadFallbackData() {
        this.blogs = [
            {
                id: 1,
                title: 'Sustainable Energy Solutions for the Future',
                excerpt: 'Discover how modern technologies help us transition to sustainable energy sources and create a greener future.',
                featured_image: 'blog/images/placeholder.jpg',
                categories: ['sustainability', 'technology'],
                author_name: 'John Johnson',
                published_at: '2024-01-15',
                slug: 'sustainable-energy-solutions'
            },
            {
                id: 2,
                title: 'Climate Cabinet Installation Guide',
                excerpt: 'Step-by-step guide for installing your new climate cabinet and optimizing energy efficiency.',
                featured_image: 'blog/images/placeholder.jpg',
                categories: ['installation', 'sustainability'],
                author_name: 'Peter Peterson',
                published_at: '2024-01-10',
                slug: 'climate-cabinet-installation-guide'
            },
            {
                id: 3,
                title: 'Innovative Climate Technology',
                excerpt: 'Explore the latest developments in climate technology and how they contribute to a more sustainable world.',
                featured_image: 'blog/images/placeholder.jpg',
                categories: ['technology', 'sustainability'],
                author_name: 'Mary de Vries',
                published_at: '2024-01-05',
                slug: 'innovative-climate-technology'
            }
        ];
    },

    getFilteredBlogs() {
        console.log('getFilteredBlogs called with blogs:', this.blogs);
        
        // Ensure blogs is an array
        if (!this.blogs || !Array.isArray(this.blogs)) {
            console.log('Blogs is not an array, returning empty');
            return [];
        }
        
        let filtered = [...this.blogs]; // Create a copy
        
        // Filter by category
        if (this.selectedCategory !== 'all') {
            console.log('Filtering by category:', this.selectedCategory);
            filtered = filtered.filter(blog => {
                if (!blog.categories || !Array.isArray(blog.categories)) {
                    console.log('Blog has no categories:', blog.title);
                    return false;
                }
                const hasCategory = blog.categories.some(cat => 
                    typeof cat === 'string' ? cat === this.selectedCategory : cat.slug === this.selectedCategory
                );
                console.log(`Blog "${blog.title}" has category ${this.selectedCategory}:`, hasCategory);
                return hasCategory;
            });
        }
        
        // Filter by search query
        if (this.searchQuery) {
            const query = this.searchQuery.toLowerCase();
            filtered = filtered.filter(blog => 
                blog.title.toLowerCase().includes(query) || 
                blog.excerpt.toLowerCase().includes(query)
            );
        }
        
        console.log('getFilteredBlogs returning:', filtered.length, 'blogs');
        return filtered;
    },

    async filterByCategory(category) {
        this.selectedCategory = category;
        this.currentPage = 1;
        this.loading = true;
        await this.loadBlogs();
        this.loading = false;
    },

    async search() {
        this.currentPage = 1;
        this.loading = true;
        await this.loadBlogs();
        this.loading = false;
    },

    async loadMore() {
        if (this.currentPage < this.totalPages) {
            this.loadingMore = true;
            this.currentPage++;
            await this.loadBlogs();
            this.loadingMore = false;
        }
    },

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    },

    computeFeaturedImageUrl(value) {
        if (!value) return '/images/placeholder.jpg';
        if (typeof value === 'string' && (value.startsWith('http://') || value.startsWith('https://') || value.startsWith('/'))){
            return value;
        }
        if (typeof value === 'string') {
            if (value.startsWith('blog/')) return `/assets/${value}`;
            if (value.startsWith('images/')) return `/assets/${value}`;
            return `/assets/blog/${value}`;
        }
        if (Array.isArray(value) && value.length > 0) {
            const first = value[0];
            if (typeof first === 'string') return first.startsWith('/') ? first : `/assets/${first.path}`;
            if (first && typeof first.path === 'string') return first.path.startsWith('/') ? first.path : `/assets/${first.path}`;
            if (first && typeof first.url === 'string') return first.url;
        }
        return '/images/placeholder.jpg';
    },

    getFirstCategory(categories) {
        if (!categories || !Array.isArray(categories) || categories.length === 0) return null;
        
        const firstCat = categories[0];
        if (typeof firstCat === 'string') {
            return { title: firstCat, slug: firstCat };
        } else if (firstCat && firstCat.title && firstCat.slug) {
            return firstCat;
        }
        
        return null;
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

// Make image URL helper available globally for Alpine expressions
window.computeFeaturedImageUrl = (value) => {
    if (!value) return '/images/placeholder.jpg';
    if (typeof value === 'string' && (value.startsWith('http://') || value.startsWith('https://') || value.startsWith('/'))){
        return value;
    }
    if (typeof value === 'string') {
        if (value.startsWith('blog/')) return `/assets/${value}`;
        if (value.startsWith('images/')) return `/assets/blog/${value}`;
        return `/assets/blog/${value}`;
    }
    if (Array.isArray(value) && value.length > 0) {
        const first = value[0];
        if (typeof first === 'string') return first.startsWith('/') ? first : `/assets/${first}`;
        if (first && typeof first.path === 'string') return first.path.startsWith('/') ? first.path : `/assets/${first.path}`;
        if (first && typeof first.url === 'string') return first.url;
    }
    return '/images/placeholder.jpg';
};

// Make category helper available globally for Alpine expressions
window.getFirstCategory = (categories) => {
    if (!categories || !Array.isArray(categories) || categories.length === 0) return null;
    
    const firstCat = categories[0];
    if (typeof firstCat === 'string') {
        return { title: firstCat, slug: firstCat };
    } else if (firstCat && firstCat.title && firstCat.slug) {
        return firstCat;
    }
    
    return null;
};