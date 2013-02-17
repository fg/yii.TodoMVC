<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Yii TodoMVC - fgursoy34</title>
	<meta name="viewport" content="width=device-width">
	<link href="<?php echo Yii::app()->request->baseUrl; ?>/style/css/style.css" media="all" type="text/css" rel="stylesheet">
</head>
<body>
		
		<section id="todoapp">

		<header id="header">
			<h1>Yii TodoMVC</h1>
			<input id="new-todo" placeholder="What needs to be done?" autofocus>
		</header>
		<section id="main">
			<input id="toggle-all" type="checkbox">
			<label for="toggle-all">Mark all as complete</label>
			<ul id="todo-list"></ul>
		</section>
		<footer id="footer"></footer>
	</section>

	<div id="info">
		<p>Welcome body,</p>
		<p>Generating by Yii (TodoMVC)</p>
		<p>fgursoy34</p>
	</div>

	<script type="text/template" id="item-template">
		<div class="view">
			<input class="toggle" type="checkbox" <%= (completed == "yes") ? 'checked' : '' %>>
			<label><%- title %></label>
			<button class="destroy"></button>
		</div>
		<input class="edit" value="<%- title %>">
	</script>
	<script type="text/template" id="stats-template">
		<span id="todo-count"><strong><%= remaining %></strong> <%= remaining === 1 ? 'item' : 'items' %> left</span>
		<ul id="filters">
			<li>
				<a class="selected" href="#/">All</a>
			</li>
			<li>
				<a href="#/active">Active</a>
			</li>
			<li>
				<a href="#/completed">Completed</a>
			</li>
		</ul>

		<% if (completed) { %>
		<button id="clear-completed">Clear Completed (<%= completed %>)</button>
		<% } %>
	</script>

	<script src="<?php echo Yii::app()->request->baseUrl; ?>/style/js/lib/json2.js"></script>
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/style/js/lib/jquery.min.js"></script>
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/style/js/lib/underscore.min.js"></script>
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/style/js/lib/backbone.min.js"></script>

	<script>
		var app = app || {};
		app.Todo = Backbone.Model.extend({

		
			defaults: {
				title: '',
				order: 0,
				completed: "no"
			},
			toggle: function() {
				this.save({
					completed: ((this.get('completed')=="yes") ? "no" : "yes")
				});
			},

			validate: function(attrs) {
				if (attrs.title == undefined ) {
				  return "Title can't be empty";
				}
			}

		});


		
		var TodoList = Backbone.Collection.extend({
			url :"<?php echo Yii::app()->createUrl('api/list');?>",
			model: app.Todo,
			completed: function() {
				return this.filter(function( todo ) {
					if (todo.get('completed') == "yes") {
						return true;
					} else {
						return false;
					}
				});
			},

			remaining: function() {
				return this.without.apply( this, this.completed() );
			},

			nextOrder: function() {
				if ( !this.length ) {
					return 1;
				}
				return this.last().get('order') + 1;
			},

			comparator: function( todo ) {
				return todo.get('order');
			}
		});


		app.Todos = new TodoList();
		app.TodoView = Backbone.View.extend({
			tagName:  'li',
			template: _.template( $('#item-template').html() ),
			events: {
				'click .toggle':	'togglecompleted',
				'dblclick label':	'edit',
				'click .destroy':	'clear',
				'keypress .edit':	'updateOnEnter',
				'blur .edit':		'close'
			},
			initialize: function() {
				this.model.on( 'change', this.render, this );
				this.model.on( 'destroy', this.remove, this );
				this.model.on( 'visible', this.toggleVisible, this );
			},
			render: function() {
				this.$el.html( this.template( this.model.toJSON() ) );
				this.$el.toggleClass( 'completed', (this.model.get('completed')=="yes" ? true : false) );
				this.toggleVisible();
				this.input = this.$('.edit');
				return this;
			},

			toggleVisible : function () {
				this.$el.toggleClass( 'hidden',  this.isHidden());
			},

			isHidden : function () {
				var isCompleted = (this.model.get('completed')=="yes") ? true : false;
				return ( // hidden cases only
					(!isCompleted && app.TodoFilter === 'completed')
					|| (isCompleted && app.TodoFilter === 'active')
				);
			},
			togglecompleted: function() {
				this.model.toggle();
			},
			edit: function() {
				this.$el.addClass('editing');
				this.input.focus();
			},
			close: function() {
				var value = this.input.val().trim();

				if ( value ) {
					this.model.save({ title: value });
				} else {
					this.clear();
				}

				this.$el.removeClass('editing');
			},
			updateOnEnter: function( e ) {
				if ( e.which === ENTER_KEY ) {
					this.close();
				}
			},
			clear: function() {
				this.model.destroy();
			}
		});
		app.AppView = Backbone.View.extend({
			el: '#todoapp',
			statsTemplate: _.template( $('#stats-template').html() ),
			events: {
				'keypress #new-todo': 'createOnEnter',
				'click #clear-completed': 'clearCompleted',
				'click #toggle-all': 'toggleAllComplete'
			},
			initialize: function() {
				this.input = this.$('#new-todo');
				this.allCheckbox = this.$('#toggle-all')[0];
				this.$footer = this.$('#footer');
				this.$main = this.$('#main');

				app.Todos.on( 'add', this.addOne, this );
				app.Todos.on( 'reset', this.addAll, this );
				app.Todos.on( 'change:completed', this.filterOne, this );
				app.Todos.on( 'filter', this.filterAll, this );
				app.Todos.on( 'all', this.render, this );

				app.Todos.fetch();
			},
			render: function() {
				var completed = app.Todos.completed().length;
				var remaining = app.Todos.remaining().length;

				if ( app.Todos.length ) {
					this.$main.show();
					this.$footer.show();

					this.$footer.html(this.statsTemplate({
						completed: completed,
						remaining: remaining
					}));

					this.$('#filters li a')
						.removeClass('selected')
						.filter('[href="#/' + ( app.TodoFilter || '' ) + '"]')
						.addClass('selected');
				} else {
					this.$main.hide();
					this.$footer.hide();
				}

				this.allCheckbox.checked = !remaining;
			},
			addOne: function( todo ) {
				var view = new app.TodoView({ model: todo });
				$('#todo-list').append( view.render().el );
			},
			addAll: function() {
				this.$('#todo-list').html('');
				app.Todos.each(this.addOne, this);
			},

			filterOne : function (todo) {
				todo.trigger('visible');
			},

			filterAll : function () {
				app.Todos.each(this.filterOne, this);
			},
			newAttributes: function() {
				return {
					title: this.input.val().trim(),
					order: app.Todos.nextOrder(),
					completed: "no"
				};
			},
			createOnEnter: function( e ) {
				if ( e.which !== ENTER_KEY || !this.input.val().trim() ) {
					return;
				}

				app.Todos.create( this.newAttributes() );
				this.input.val('');
			},
			clearCompleted: function() {
				_.each( app.Todos.completed(), function( todo ) {
					todo.destroy();
				});

				return false;
			},

			toggleAllComplete: function() {
				var completed = this.allCheckbox.checked;

				app.Todos.each(function( todo ) {
					todo.save({
						'completed': (completed ? "yes" : "no")
					});
				});
			}
		});


		var Workspace = Backbone.Router.extend({
			routes:{
				'*filter': 'setFilter'
			},

			setFilter: function( param ) {
				app.TodoFilter = param.trim() || '';
				app.Todos.trigger('filter');
			}
		});

		app.TodoRouter = new Workspace();
		Backbone.history.start();

		var ENTER_KEY = 13;

		$(function() {
			new app.AppView();
		});


	</script>
</body>
</html>
