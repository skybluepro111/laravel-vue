// App Template
var App = Vue.extend({
	template: '#apptemplate'
});

// Home Template
var Home = Vue.extend({
	template: '#hometemplate'
});

// LogOut Template
var LogOut = Vue.extend({
	template: '#logout',
	ready: function(){
		localStorage.removeItem("jwt_token");
		localStorage.jwt_token = null;
		jwt_token = null;
	}
});

// Signup Template
var Signup = Vue.extend({
	template: '#signup',
	data: function() {
		return {
			credentials: {
				name: '',
				email: '',
				username: '',
				password: ''
			},
			errors: [],
			success: '',
		}
	},
	methods: {
		submit: function() {
			var credentials = {
				name: this.credentials.name,
				email: this.credentials.email,
				username: this.credentials.username,
				password: this.credentials.password
			};
			this.$http.post('/api/signup', credentials).then((data) => {
				this.success = 'New account has been created successfully. Login now.';
				this.credentials.name = '';
				this.credentials.email = '';
				this.credentials.username = '';
				this.credentials.password = '';
			}, (error) => {
	        	this.errors = error.data.validation;
			});
		}
    }
});

// Login Template
var Login = Vue.extend({
	template: '#login',
	data: function() {
		return {
			credentials: {
				email: '',
				password: ''
			},
			errors: [],
			success: '',
		}
	},
	methods: {
		submit: function() {
			var credentials = {
				email: this.credentials.email,
				password: this.credentials.password
			}
			this.$http.post('/api/signin', credentials).then((data) => {
				localStorage.removeItem("jwt_token");
				localStorage.jwt_token = data.data.token;
				jwt_token = data.data.token;
			}, (error) => {
	        	this.errors = error.data.validation;
			});
		}
    }
});

// Forum Template
var Forum = Vue.extend({
	template: '#forum',
	data: function(){
		return {
            channel_lists: [],
			discussions: [],
            offset: 4,
            filter_by: 'all',
            channel: 'all',
            pagination: {
                per_page: 20,
                current_page: 1,
                total: 0,
                from: 1,
                to: 0,
                last_page: 1,
            }
		}
	},
	ready: function(){
		this.fetchDiscuss(this.pagination.current_page, jwt_token);
	},
    computed: {
        isActived: function(){
            return this.pagination.current_page;
        },
        pagesNumber: function(){
            if (!this.pagination.to) {
                return [];
            }
            var from = this.pagination.current_page - this.offset;
            if (from < 1) {
                from = 1;
            }
            var to = from + (this.offset * 2);
            if (to >= this.pagination.last_page) {
                to = this.pagination.last_page;
            }
            var pagesArray = [];
            while (from <= to) {
                pagesArray.push(from);
                from++;
            }

            return pagesArray;
        },
        hasDiscussion: function(){
            if (this.pagination.total > 0) {
                return true;
            }
            return false;
        }
    },
	methods: {
		fetchDiscuss: function(page, token){
			this.$http.get(
				'/api/discuss',
				{
                    page: page,
                    channel: this.channel,
                    filter_by: this.filter_by,
                    token: token
                }).then((result) => {
				this.channel_lists = result.data.data.channels;
				this.discussions = result.data.data.discuss;
				this.pagination = result.data.data.pagination;
			});
		},
        changePage: function (page) {
            this.pagination.current_page = page;
            this.fetchDiscuss(page, jwt_token);
        },
        filterBy: function(filter_by){
            this.filter_by = filter_by;
            this.channel = 'all';
            this.fetchDiscuss(1, jwt_token);
        },
        filterByChannel: function(channel){
            this.filter_by = 'all';
            this.channel = channel;
            this.fetchDiscuss(1, jwt_token);
        }
	}
});

// Vue Router
var router = new VueRouter()
router.map({
    '/home': {
        component: Home
    },
	'forum': {
		component: Forum
	},
    '/signup': {
        component: Signup
    },
    '/login': {
        component: Login
    },
    '/logout': {
        component: LogOut
    }
});
router.redirect({ '*': '/home' });
router.start(App, '#app');

new Vue({
	el: 'body'
});