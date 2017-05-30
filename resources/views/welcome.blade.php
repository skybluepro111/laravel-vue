<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vue JS</title>
    <link rel="stylesheet" href="./assets/libs/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/style.min.css">
</head>
<body>
    <div id="app"></div>

    <template id="apptemplate">
        <div>
            <nav class="navbar navbar-default">
                <div class="container">
                    <ul class="nav navbar-nav">
                        <li><a v-link="'home'">Home</a></li>
                        <li><a v-link="'forum'">Forum</a></li>
                        <li><a v-link="'signup'">Sign Up</a></li>
                        <li><a v-link="'login'">Login</a></li>
                        <li><a v-link="'logout'">Logout</a></li>
                    </ul>
                </div>
            </nav>
            <div class="container">
                <router-view></router-view>
            </div>
        </div>
    </template>

    <template id="hometemplate">
        <div class="col-sm-6 col-sm-offset-3">
            <h1>Laravel and Vuejs</h1>
            <p>One page forum application.</p>
            <ul>
                <li>Features</li>
                <ul>
                    <li>REST API</li>
                    <li>JWT Auth</li>
                </ul>
            </ul>
        </div>
    </template>

    <template id="forum">
        <div class="row" id="discussions">
            <div class="col-md-3">
                <a href="" class="btn btn-danger btn-block">New Discussion</a>
                <br>
                <ul class="Filter">
                    <li>
                        <a href="#">Filters</a>
                        <ul>
                            <li><a href="#" v-on:click.prevent="filterBy('me')">My Questions</a></li>
                            <li><a href="#" v-on:click.prevent="filterBy('contributed_to')">My Participation</a></li>
                            <li><a href="#" v-on:click.prevent="filterBy('popular_this_week')">Popular This Week</a></li>
                            <li><a href="#" v-on:click.prevent="filterBy('popular_all_time')">Popular All Time</a></li>
                            <li><a href="#" v-on:click.prevent="filterBy('answered')">Answered Questions</a></li>
                            <li><a href="#" v-on:click.prevent="filterBy('unanswered')">Unanswered Questions</a></li>
                            <li><a href="#" v-on:click.prevent="filterBy('all')">Reset</a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="channel_lists">
                    <li><a href="#" v-on:click.prevent="filterByChannel('all')">All</a></li>
                    <li v-for="list in channel_lists">
                        <a href="#" v-on:click.prevent="filterByChannel(list.slug)">@{{ list.name }}</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9">
                <div class="topic-list" v-if="hasDiscussion">
                    <div class="topic-list__topic" v-for="discussion in discussions">
                        <div class="topic-list__user">
                            <div class="user-avatar">
                                <img
                                    alt="@{{ discussion.user.name }}"
                                    v-bind:src="discussion.user.avatar"
                                    class="user-avatar__image"
                                    width="45"
                                >
                            </div>
                        </div>
                        <div class="topic-list__content">
                            <h5 class="topic-list__topic-title">
                                <a v-bind:href="discussion.url">@{{ discussion.title }}</a>
                            </h5>
                            <div class="topic-list__collapse">
                                <div class="columns first_column">
                                    <p><span class="user__name">@{{ discussion.user.name }}</span> - <span class="post__date">@{{ discussion.posted_at }}</span></p>
                                    <ul class="list-inline">
                                        <li><a href="#" v-on:click.prevent="filterByChannel(discussion.channel.slug)">@{{ discussion.channel.name }}</a></li>
                                    </ul>
                                </div>
                                <div class="columns last_column">
                                    <div class="topic-list__answer-container" v-if="discussion.comment_count > 1">
                                        <span class="topic-list__answer-figure">@{{ discussion.comment_count }}</span>answers
                                    </div>
                                    <div class="topic-list__answer-container" v-else>
                                        <span class="topic-list__answer-figure">@{{ discussion.comment_count }}</span>answer
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else>
                    <p>Sorry, Nothing is available here yet.</p>
                </div>
                <nav v-if="hasDiscussion">
                    <ul class="pagination">
                        <li v-if="pagination.current_page > 1">
                            <a href="#" aria-label="Previous" v-on:click.prevent="changePage(pagination.current_page - 1)">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <li class="disabled" v-else><span aria-hidden="true">&laquo;</span></li>
                        <li v-for="page in pagesNumber" v-bind:class="[ page == isActived ? 'active' : '']">
                            <a href="#" v-on:click.prevent="changePage(page)">@{{ page }}</a>
                        </li>
                        <li v-if="pagination.current_page < pagination.last_page">
                            <a href="#" aria-label="Next" v-on:click.prevent="changePage(pagination.current_page + 1)">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <li class="disabled" v-else><span aria-hidden="true">&raquo;</span></li>
                    </ul>
                </nav>
            </div><!-- #discussions -->
        </div>
    </template>

    <template id="login">
        <div class="col-sm-4 col-sm-offset-4">
            <h2>Log In</h2>
            <div class="alert alert-success" v-if="success">
                <p>@{{ success }}</p>
            </div>
            <div class="form-group" v-bind:class="[errors.email ? 'has-error' : '']">
                <input type="text" class="form-control" placeholder="Email" v-model="credentials.email">
                <span class="help-block" v-if="errors.email">@{{ errors.email[0] }}</span>
            </div>
            <div class="form-group" v-bind:class="[errors.password ? 'has-error' : '']">
                <input type="password" class="form-control" placeholder="Password" v-model="credentials.password">
                <span class="help-block" v-if="errors.password">@{{ errors.password[0] }}</span>
            </div>
            <button class="btn btn-primary" @click="submit()">Access</button>
        </div>
    </template>

    <template id="logout">
        <div class="col-sm-6 col-sm-offset-3">
            <h1>You are logged out!</h1>
        </div>
    </template>

    <template id="signup">
        <div class="col-sm-4 col-sm-offset-4">
            <h2>Sign Up</h2>
            <div class="alert alert-success" v-if="success">
                <p>@{{ success }}</p>
            </div>
            <div class="form-group" v-bind:class="[errors.name ? 'has-error' : '']">
                <input type="text" class="form-control" placeholder="Name" v-model="credentials.name">
                <span class="help-block" v-if="errors.name">@{{ errors.name[0] }}</span>
            </div>
            <div class="form-group" v-bind:class="[errors.email ? 'has-error' : '']">
                <input type="text" class="form-control" placeholder="Email" v-model="credentials.email">
                <span class="help-block" v-if="errors.email">@{{ errors.email[0] }}</span>
            </div>
            <div class="form-group" v-bind:class="[errors.username ? 'has-error' : '']">
                <input type="text" class="form-control" placeholder="Username" v-model="credentials.username">
                <span class="help-block" v-if="errors.username">@{{ errors.username[0] }}</span>
            </div>
            <div class="form-group" v-bind:class="[errors.password ? 'has-error' : '']">
                <input type="password" class="form-control" placeholder="Password" v-model="credentials.password">
                <span class="help-block" v-if="errors.password">@{{ errors.password[0] }}</span>
            </div>
            <button class="btn btn-primary" @click="submit()">submit</button>
        </div>
    </template>

    <script type="text/javascript" src="./assets/libs/vuejs/vue.js"></script>
    <script type="text/javascript" src="./assets/libs/vuejs/vue-resource.min.js"></script>
    <script type="text/javascript" src="./assets/libs/vuejs/vue-router.min.js"></script>
    <script type="text/javascript">
        var authenticated = false;
        var jwt_token = localStorage.jwt_token;
    </script>
    <script type="text/javascript" src="./assets/js/app.js"></script>
</body>
</html>