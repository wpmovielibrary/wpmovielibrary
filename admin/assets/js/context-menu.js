/**
 * ContextMenu.
 *
 * @since 3.0.0
 *
 * @package ContextMenu
 */

contextMenu = window.contextMenu;

(function( $, _, Backbone ) {

	/**
	 * ContextMenu Controller.
	 *
	 * @since 1.0.0
	 */
	contextMenu = Backbone.Model.extend({

		defaults : {
			coordinates : {
				x : 10,
				y : 10,
			},
			groups : [],
		},

		/**
		 * Initialize the controller.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		initialize : function( attributes, options ) {

			this.data = new Backbone.Model;

			this.groups = new contextMenuGroups;

			return this;
		},

		/**
		 * Set model data.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} data
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		setData : function( data, options ) {

			if ( _.isFunction( data.toJSON ) ) {
				data = data.toJSON();
			}

			this.data.set( data, options );

			return this;
		},

		/**
		 * Retrieve model data.
		 *
		 * If a key is passed, return the corresponding data, if nay. Return all data
		 * otherwise.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} key
		 *
		 * @return {mixed}
		 */
		getData : function( key ) {

			return this.data.has( key ) ? this.data.get( key ) : this.data;
		},

		/**
		 * Position the menu.
		 *
		 * @since 1.0.0
		 *
		 * @param {int} x Horizontal position.
		 * @param {int} y Vertical position.
		 *
		 * @return Returns itself to allow chaining.
		 */
		setPosition : function( x, y ) {

			this.set( 'coordinates', {
				x : _.isNumber( x ) ? x : 0,
				y : _.isNumber( y ) ? y : 0,
			} );

			return this;
		},

		/**
		 * Add a list of group to the menu.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} groups List of group.
		 *
		 * @return Returns itself to allow chaining.
		 */
		addGroups : function( groups ) {

			_.map( groups || [], this.addGroup, this );

			return this;
		},

		/**
		 * Add a group to the menu.
		 *
		 * If group.position is set, add the group at this position within
		 * the collection.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} group Group attributes.
		 *
		 * @return Returns itself to allow chaining.
		 */
		addGroup : function( group ) {

			var options = {};
			if ( _.isNumber( group.position ) ) {
				options.at = group.position;
			}

			var group = new contextMenuGroup( group, {
				controller : this,
			} );

			this.groups.add( group, options );

			return this;
		},

		/**
		 * Retrieve a group of items.
		 *
		 * @since 1.0.0
		 *
		 * @param {mixed} group Group ID, instance of attributes.
		 *
		 * @return {object} Group instance.
		 */
		getGroup : function( group ) {

			return this.groups.get( group );
		},

		/**
		 * Remove a group from the collection.
		 *
		 * @since 1.0.0
		 *
		 * @param {mixed} group Group ID, instance or attributes.
		 *
		 * @return Returns itself to allow chaining.
		 */
		removeGroup : function( group ) {

			this.groups.remove( group );

			return this;
		},

		/**
		 * Open the menu.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		open : function( options ) {

			if ( this.menu ) {
				this.close( options );
			}

			this.menu = new contextMenuView( { controller : this } );
			this.menu.open();

			return this;
		},

		/**
		 * Close the menu.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		close : function( options ) {

			var options = _.defaults( {
				clear : false,
			}, options );

			if ( this.menu ) {
				this.menu.remove();
				delete this.menu;
			}

			if ( this.data && options.clear ) {
				this.data.clear();
			}

			return this
		},

	});

	/**
	 * ContextMenu Item.
	 *
	 * @since 1.0.0
	 */
	var contextMenuItem = Backbone.Model.extend({

		defaults : {
			position   : -1,
			icon       : '',
			title      : '',
			selectable : false,
			multiple   : false,
			selected   : false,
			action     : '',
			field      : '',
			value      : '',
			groups     : [],
		},

		/**
		 * Initialize the Model.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.controller = options.controller;

			this.group     = options.group;
			this.selection = this.group.selection;

			this.groups = new contextMenuGroups;
			if ( attributes.groups ) {
				this.addGroups( attributes.groups );
			}

			if ( attributes.selected && _.isTrue( attributes.selected ) ) {
				this.selection.add( this );
			}

			this.on( 'change', this.updateSelection, this );
			this.on( 'action', function() {
				this.controller.close();
			}, this );

			return this;
		},

		/**
		 * Update group selection to include/exclude item.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} model
		 * @param {object} value
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		updateSelection : function( model, value, options ) {

			if ( ! _.has( model.changed, 'selected' ) ) {
				return this;
			}

			if ( _.isTrue( model.changed.selected ) ) {
				this.set( 'selectable', true );
				if ( ! this.isMultiple() ) {
					this.selection.reset();
				}
				this.selection.add( this );
			} else {
				this.selection.remove( this );
			}

			return this;
		},

		/**
		 * Set item position.
		 *
		 * @since 1.0.0
		 *
		 * @param {int} position
		 *
		 * @return Returns itself to allow chaining.
		 */
		setPosition : function( position ) {

			this.set( 'position', position );

			return this;
		},

		/**
		 * Set item icon.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} icon
		 *
		 * @return Returns itself to allow chaining.
		 */
		setIcon : function( icon ) {

			this.set( 'icon', icon );

			return this;
		},

		/**
		 * Set item title.
		 *
		 * @since 1.0.0
		 *
		 * @param {string} title
		 *
		 * @return Returns itself to allow chaining.
		 */
		setTitle : function( title ) {

			this.set( 'title', title );

			return this;
		},

		/**
		 * Is item selectable?
		 *
		 * @since 1.0.0
		 *
		 * @return {boolean}
		 */
		isSelectable : function() {

			return _.isTrue( this.get( 'selectable' ) );
		},

		/**
		 * Is item multiple selectable?
		 *
		 * @since 1.0.0
		 *
		 * @return {boolean}
		 */
		isMultiple : function() {

			return _.isTrue( this.get( 'multiple' ) );
		},

		/**
		 * Set item as multiple selectable.
		 *
		 * @since 1.0.0
		 *
		 * @param {boolean} multiple
		 *
		 * @return Returns itself to allow chaining.
		 */
		setMultiple : function( multiple ) {

			this.set( 'multiple', _.isTrue( multiple ) );

			return this;
		},

		/**
		 * Set item as selectable.
		 *
		 * @since 1.0.0
		 *
		 * @param {boolean} selectable
		 *
		 * @return Returns itself to allow chaining.
		 */
		setSelectable : function( selectable ) {

			this.set( 'selectable', _.isTrue( selectable ) );

			return this;
		},

		/**
		 * Is item selected?
		 *
		 * @since 1.0.0
		 *
		 * @return {boolean}
		 */
		isSelected : function() {

			return _.isTrue( this.get( 'selected' ) );
		},

		/**
		 * Set item as selected.
		 *
		 * @since 1.0.0
		 *
		 * @param {boolean} selected
		 *
		 * @return Returns itself to allow chaining.
		 */
		setSelected : function( selected ) {

			this.set( 'selected', _.isTrue( selected ) );

			return this;
		},

		/**
		 * Add a list of groups.
		 *
		 * @since 1.0.0
		 *
		 * @param {array} groups List of groups.
		 *
		 * @return Returns itself to allow chaining.
		 */
		addGroups : function( groups ) {

			_.map( groups || [], this.addGroup, this );

			return this;
		},

		/**
		 * Add a group to the collection.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} group Group attributes.
		 *
		 * @return Returns itself to allow chaining.
		 */
		addGroup : function( group ) {

			var options = {};
			if ( _.isNumber( group.position ) ) {
				options.at = group.position;
			}

			var group = new contextMenuGroup( group, {
				controller : this,
			} );

			this.groups.add( group, options );

			return this;
		},

		/**
		 * Retrieve a group.
		 *
		 * @since 1.0.0
		 *
		 * @param {mixed} group Group ID, instance or attributes.
		 *
		 * @return {object} Group instance.
		 */
		getGroup : function( group ) {

			return this.groups.get( group );
		},

		/**
		 * Remove a group from the collection.
		 *
		 * @since 1.0.0
		 *
		 * @param {mixed} group Group ID, instance or attributes.
		 *
		 * @return Returns itself to allow chaining.
		 */
		removeGroup : function( group ) {

			this.groups.remove( group );

			return this;
		},

		/**
		 * JSONify model.
		 *
		 * @since 1.0.0
		 *
		 * @return {object}
		 */
		toJSON : function() {

			return _.extend( Backbone.Model.prototype.toJSON.call( this, arguments ) || {}, {
				groups : this.groups.toJSON() || [],
			} );
		},

	});

	/**
	 * ContextMenu Item Collection.
	 *
	 * @since 1.0.0
	 */
	var contextMenuItems = Backbone.Collection.extend({

		model : contextMenuItem,

		comparator : 'position',

		/**
		 * Initialize the Collection.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} models
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		initialize : function( models, options ) {

			this.on( 'change:position', this.sort, this );

			this.on( 'update', this.sort, this );

			return this;
		},

	});

	/**
	 * ContextMenu Group.
	 *
	 * @since 1.0.0
	 */
	var contextMenuGroup = Backbone.Model.extend({

		defaults : {
			position : -1,
		},

		/**
		 * Initialize the model.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} attributes
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		initialize : function( attributes, options ) {

			var options = options || {};

			this.items      = new contextMenuItems;
			this.selection  = new Backbone.Collection;
			this.controller = options.controller;

			if ( attributes.items ) {
				this.addItems( attributes.items );
			}

			this.listenTo( this.selection, 'update', function() {
				this.trigger( 'selection', this.getSelection() );
			} );

			this.listenTo( this.selection, 'reset', function( collection, options ) {
				_.map( options.previousModels, function( model ) {
					model.setSelected( false );
				}, this );
			} );

			return this;
		},

		/**
		 * Retrieve group selected items.
		 *
		 * @since 1.0.0
		 *
		 * @return {array}
		 */
		getSelection : function() {

			var selection = _.map( this.selection.toJSON() || {}, function( item ) {
				return _.pick( item, 'field', 'value' );
			}, this );

			return selection;
		},

		/**
		 * Set group position.
		 *
		 * @since 1.0.0
		 *
		 * @param {int} position
		 *
		 * @return Returns itself to allow chaining.
		 */
		setPosition : function( position ) {

			return this.set( 'position', position );
		},

		/**
		 * Add a list of items to the collection.
		 *
		 * @since 1.0.0
		 *
		 * @param {array} items List of items.
		 *
		 * @return Returns itself to allow chaining.
		 */
		addItems : function( items ) {

			_.map( items || [], this.addItem, this );

			return this;
		},

		/**
		 * Add an item to the collection.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} item Item attributes.
		 *
		 * @return Returns itself to allow chaining.
		 */
		addItem : function( item ) {

			var options = {};
			if ( _.isNumber( item.position ) ) {
				options.at = item.position;
			}

			var item = new contextMenuItem( item, {
				group      : this,
				controller : this.controller,
			} );

			this.items.add( item, options );

			return this;
		},

		/**
		 * Get items collection.
		 *
		 * @since 1.0.0
		 *
		 * @return {object} Item instance.
		 */
		getItems : function() {

			return this.items;
		},

		/**
		 * Retrieve an item.
		 *
		 * @since 1.0.0
		 *
		 * @param {mixed} item Item ID, instance or attributes.
		 *
		 * @return {object} Item instance.
		 */
		getItem : function( item ) {

			return this.items.get( item );
		},

		/**
		 * Remove an item from the collection.
		 *
		 * @since 1.0.0
		 *
		 * @param {mixed} item Item ID, instance or attributes.
		 *
		 * @return Returns itself to allow chaining.
		 */
		removeItem : function( item ) {

			this.items.remove( item );

			return this;
		},

		/**
		 * JSONify the model.
		 *
		 * @since 1.0.0
		 *
		 * @return {object}
		 */
		toJSON : function() {

			return _.extend( Backbone.Model.prototype.toJSON.call( this, arguments ) || {}, {
				items : this.items.toJSON() || [],
			} );
		},

	});

	/**
	 * ContextMenu Group Collection.
	 *
	 * @since 1.0.0
	 */
	var contextMenuGroups = Backbone.Collection.extend({

		model : contextMenuGroup,

		comparator : 'position',

		/**
		 * Initialize the Collection.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} models
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		initialize : function( models, options ) {

			this.on( 'change:position', this.sort, this );

			this.on( 'update', this.sort, this );

			return this;
		},

	});

	/**
	 * ContextMenuItem View.
	 *
	 * @since 1.0.0
	 */
	var contextMenuItemView = wp.Backbone.View.extend({

		tagName : 'li',

		className : 'context-menu-item',

		template : wp.template( 'wpmoly-context-menu-item' ),

		events : {
			'click' : 'onClick',
		},

		viewsByCid : {},

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		initialize : function( options ) {

			var options = options || {};

			this.model      = options.model;
			this.controller = options.controller;

			this.listenTo( this.model.groups, 'update', this.render );
			this.listenTo( this.model.groups, 'sort',   this.render );

			this.listenTo( this.model.selection, 'update', this.render );

			return this;
		},

		/**
		 * Item clicked.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event JS 'click' Event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		onClick : function( event ) {

			event.stopPropagation();

			if ( this.$el.hasClass( 'has-groups' ) ) {
				return this;
			}

			if ( this.model.isSelectable() ) {
				if ( this.model.isSelected() ) {
					this.model.setSelected( false );
				} else {
					this.model.setSelected( true );
				}
			} else {
				var item = this.model.toJSON(),
				  action = this.model.get( 'action' );
				this.model.trigger( 'action', action, item );
				this.model.trigger( 'action:' + action, item );
			}

			return this;
		},

		/**
		 * Add all group views.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		addGroups : function() {

			if ( this.model.groups.length ) {
				this.model.groups.map( this.addGroup, this );
			}

			return this;
		},

		/**
		 * Add a new group view.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} model
		 * @param {object} collection
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		addGroup : function( model, collection, options ) {

			var group = new contextMenuGroupView({
				model      : model,
			 	controller : this.controller,
			});

			this.viewsByCid[ model.id ] = group;

			this.views.add( '.context-sub-menu-content', group );

			return this;
		},

		/**
		 * Remove all group views.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		removeGroups : function() {

			if ( this.model.groups.length ) {
				this.model.groups.map( this.removeGroup, this );
			}

			return this;
		},

		/**
		 * Remove a group view.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} model
		 * @param {object} collection
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		removeGroup : function( model, collection, options ) {

			var view = this.viewsByCid[ model.id ];

			view.remove();
			delete this.viewsByCid[ model.id ];

			return this;
		},

		/**
		 * Prepare rendering options.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		prepare : function() {

			var options = this.model.toJSON() || {};

			return options;
		},

		/**
		 * Render the View.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		render : function() {

			wp.Backbone.View.prototype.render.apply( this, arguments );

			this.el.id = 'item-' + this.model.get( 'id' );

			if ( ! this.model.isSelectable() ) {
				this.$el.attr( 'data-item', this.model.get( 'id' ) );
			}

			if ( this.model.groups.length ) {
				this.$el.addClass( 'has-groups' );
			}

			this.views.remove();
			this.addGroups();

			return this;
		},

	});

	/**
	 * ContextMenuGroup View.
	 *
	 * @since 1.0.0
	 */
	var contextMenuGroupView = wp.Backbone.View.extend({

		tagName : 'ul',

		className : 'context-menu-group',

		viewsByCid : {},

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		initialize : function( options ) {

			var options = options || {};

			this.model      = options.model;
			this.controller = options.controller;

			this.listenTo( this.model.items, 'add',    this.addItem );
			this.listenTo( this.model.items, 'remove', this.removeItem );

			return this;
		},

		/**
		 * Add all item views.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		addItems : function() {

			if ( this.model.items.length ) {
				this.model.items.map( this.addItem, this );
			}

			return this;
		},

		/**
		 * Add a new item view.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} model
		 * @param {object} collection
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		addItem : function( model, collection, options ) {

			var group = new contextMenuItemView({
				model      : model,
			 	controller : this.controller,
			});

			this.viewsByCid[ model.id ] = group;

			this.views.add( group );

			return this;
		},

		/**
		 * Remove all item views.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		removeItems : function() {

			if ( this.model.items.length ) {
				this.model.items.map( this.removeItem, this );
			}

			return this;
		},

		/**
		 * Remove an item view.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} model
		 * @param {object} collection
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		removeItem : function( model, collection, options ) {

			var view = this.viewsByCid[ model.id ];

			view.remove();
			delete this.viewsByCid[ model.id ];

			return this;
		},

		/**
		 * Render the View.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		render : function() {

			wp.Backbone.View.prototype.render.apply( this, arguments );

			this.el.id = 'group-' + this.model.get( 'id' );

			this.addItems();

			return this;
		},

	});

	/**
	 * ContextMenu View.
	 *
	 * @since 1.0.0
	 */
	var contextMenuView = wp.Backbone.View.extend({

		className : 'context-menu-content',

		events : {
			'click'               : 'stopPropagation',
			'contextmenu'         : 'stopPropagation',
		},

		viewsByCid : {},

		/**
		 * Initialize the View.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		initialize : function( options ) {

			var options = options || {};

			this.controller = options.controller;

			this.listenTo( this.controller, 'change:coordinates', this.setPosition );

			this.listenTo( this.controller.groups, 'update', this.render );
			this.listenTo( this.controller.groups, 'sort',   this.render );

			return this;
		},

		/**
		 * Stop event propagation to avoid impromptusly closing the menu.
		 *
		 * @since 3.0.0
		 *
		 * @param {object} JS 'click' or 'contextmenu' Event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		stopPropagation : function( event ) {

			event.stopPropagation();

			return this;
		},

		/**
		 * Add all group views.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		addGroups : function() {

			if ( this.controller.groups.length ) {
				this.controller.groups.map( this.addGroup, this );
			}

			return this;
		},

		/**
		 * Add a new group view.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} model
		 * @param {object} collection
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		addGroup : function( model, collection, options ) {

			var group = new contextMenuGroupView({
				model      : model,
			 	controller : this.controller,
			});

			this.viewsByCid[ model.id ] = group;

			this.views.add( group );

			return this;
		},

		/**
		 * Remove all group views.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		removeGroups : function() {

			if ( this.controller.groups.length ) {
				this.controller.groups.map( this.removeGroup, this );
			}

			return this;
		},

		/**
		 * Remove a group view.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} model
		 * @param {object} collection
		 * @param {object} options
		 *
		 * @return Returns itself to allow chaining.
		 */
		removeGroup : function( model, collection, options ) {

			var view = this.viewsByCid[ model.id ];

			view.remove();
			delete this.viewsByCid[ model.id ];

			return this;
		},

		/**
		 * Open Context Menu.
		 *
		 * @since 3.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		open : function() {

			var self = this;

			// Avoid losing events when closing.
			self.delegateEvents();

			// Add view to DOM.
			$( 'body' ).append( '<div id="context-menu-' + this.cid + '" class="wpmoly context-menu" />' );
			$( '#context-menu-' + this.cid ).append( self.render().$el );

			// Bind closing events.
			_.delay( function() {
				$( 'body' ).one( 'contextmenu', _.bind( self.close, self ) );
			}, 50 );
			$( 'body' ).one( 'click', _.bind( self.close, self ) );
			$( 'body' ).one( 'keydown', _.bind( self.close, self ) );
			$( window ).one( 'resize', _.bind( self.close, self ) );

			return this;
		},

		/**
		 * Close Context Menu.
		 *
		 * @since 3.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		close : function() {

			// Remove view.
			this.controller.close();
			$( '#context-menu-' + this.cid ).remove();

			return this;
		},

		/**
		 * Position context menu.
		 *
		 * @since 3.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		setPosition : function() {

			var $contextMenu = $( '#context-menu-' + this.cid );

			$contextMenu.addClass( 'active' );

			var position = this.controller.get( 'coordinates' ) || {},
			   overflowX = ( window.innerWidth <= ( position.x + 200 ) ),
			suboverflowX = ( window.innerWidth <= ( position.x + 400 ) ),
			   overflowY = ( window.innerHeight <= ( position.y + $contextMenu.height() ) );

			$contextMenu.css({
				left : ( overflowX ? ( position.x - $contextMenu.innerWidth() ) : position.x ) || 0,
				top  : ( overflowY ? ( position.y - $contextMenu.innerHeight() ) : position.y ) || 0,
			});

			$contextMenu.toggleClass( 'sub-menu-left', suboverflowX );
			$contextMenu.toggleClass( 'sub-menu-bottom', overflowY );

			return this;
		},

		/**
		 * Prepare rendering options.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		prepare : function() {

			var options = {
				groups : this.controller.groups.toJSON() || [],
			};

			return options;
		},

		/**
		 * Render the View.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		render : function() {

			wp.Backbone.View.prototype.render.apply( this, arguments );

			_.delay( _.bind( this.setPosition, this ), 20 );

			this.views.remove();
			this.addGroups();

			return this;
		},

	});

})( jQuery, _, Backbone );

/*jQuery( document ).ready( function( $ ) {

	testcontextmenu = window.testcontextmenu = {};

	// Testing.
	$( 'body' ).on( 'contextmenu', function( event ) {

		event.preventDefault();

		if ( testcontextmenu.close ) {
			testcontextmenu.close();
		}

		testcontextmenu = new contextMenu({
			coordinates : {
				x : event.pageX,
				y : event.pageY,
			}
		});

		testcontextmenu.addGroup( { id : 'group-a' } );

		testcontextmenu.getGroup( 'group-a' ).setPosition( 1 );

		testcontextmenu.getGroup( 'group-a' ).addItems([
			{
				id       : 'item-a-a',
				icon     : 'dashicons dashicons-wordpress',
				title    : 'Item A > A',
				position : 0,
			}, {
				id       : 'item-a-b',
				icon     : 'dashicons dashicons-wordpress',
				title    : 'Item A > B',
				position : 1,
			},
		]);

		testcontextmenu.getGroup( 'group-a' ).addItem( { id : 'item-a-c' } );
		testcontextmenu.getGroup( 'group-a' ).getItem( 'item-a-c' ).setPosition( 2 );
		testcontextmenu.getGroup( 'group-a' ).getItem( 'item-a-c' ).setIcon( 'dashicons dashicons-wordpress' );
		testcontextmenu.getGroup( 'group-a' ).getItem( 'item-a-c' ).setTitle( 'Item A > C' );

		testcontextmenu.getGroup( 'group-a' ).getItem( 'item-a-b' ).addGroup({
			id : 'group-a-b-a',
			items : [
				{
					id         : 'item-a-b-a-a',
					icon       : '',
					title      : 'Item A > B > A > A',
					selectable : true,
					field      : 'field-a-b-a-a',
					value      : 'FOO',
				}, {
					id         : 'item-a-b-a-b',
					icon       : '',
					title      : 'Item A > B > A > B',
					selectable : true,
					selected   : true,
					field      : 'field-a-b-a-b',
					value      : 'BAR',
				},
			],
		});

		testcontextmenu.addGroup({
			id       : 'group-b',
			position : 0,
			items    : [
				{
					id       : 'item-b-a',
					action   : 'action-b-a',
					icon     : 'dashicons dashicons-wordpress-alt',
					title    : 'Item B > A',
					position : 1,
				}, {
					id       : 'item-b-b',
					action   : 'action-b-b',
					icon     : 'dashicons dashicons-wordpress-alt',
					title    : 'Item B > B',
					position : 0,
				},
			],
		});

		testcontextmenu.getGroup( 'group-a' ).getItem( 'item-a-b' ).getGroup( 'group-a-b-a' ).on( 'selection', console.log );
		testcontextmenu.getGroup( 'group-b' ).getItem( 'item-b-a' ).on( 'action', console.log );
		testcontextmenu.getGroup( 'group-b' ).getItem( 'item-b-b' ).on( 'action', console.log );

		testcontextmenu.open();

	} );

} );*/
