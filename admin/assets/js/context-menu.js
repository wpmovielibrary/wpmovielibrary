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
	 * ContextMenu Item.
	 *
	 * @since 1.0.0
	 */
	var contextMenuItem = Backbone.Model.extend({

		defaults : {
			position : -1,
			icon     : '',
			title    : '',
			groups   : [],
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

			this.groups = new contextMenuGroups;

			this.mirrorEvents();

			if ( attributes.groups ) {
				this.addGroups( attributes.groups );
			}

			return this;
		},

		/**
		 * Mirror group collection events.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		mirrorEvents : function() {

			var self = this,
			  groups = self.groups;

			self.listenTo( groups, 'add', function( model, collection, options ) {
				self.trigger( 'change', self, self.collection, options );
			} );

			self.listenTo( groups, 'update', function( collection, options ) {
				self.trigger( 'change', self, self.collection, options );
			} );

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

			this.items = new contextMenuItems;

			this.mirrorEvents();

			if ( attributes.items ) {
				this.addItems( attributes.items );
			}

			return this;
		},

		/**
		 * Mirror item collection events.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		mirrorEvents : function() {

			var self = this,
			   items = self.items;

			self.listenTo( items, 'update', function( collection, options ) {
				self.trigger( 'update', self, options );
			} );

			self.listenTo( items, 'change', function( collection, options ) {
				self.trigger( 'update', self, options );
			} );

			self.listenTo( items, 'sort', function( collection, options ) {
				self.trigger( 'update', self, options );
			} );

			return this;
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

			return this;
		},

	});

	/**
	 * ContextMenu View.
	 *
	 * @since 1.0.0
	 */
	var contextMenuView = wp.Backbone.View.extend({

		className : 'wpmoly context-menu',

		template : wp.template( 'wpmoly-context-menu' ),

		events : {
			'click'               : 'stopPropagation',
			'contextmenu'         : 'stopPropagation',
			'click [data-item]'   : 'onClick',
			'change [data-field]' : 'onChange',
		},

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
		 * Item clicked.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event JS 'click' Event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		onClick : function( event ) {

			var $target = this.$( event.currentTarget ),
			       item = $target.attr( 'data-item' );

			this.controller.trigger( 'contextmenu:action', item );

			return this;
		},

		/**
		 * Item changed.
		 *
		 * @since 1.0.0
		 *
		 * @param {object} event JS 'change' Event.
		 *
		 * @return Returns itself to allow chaining.
		 */
		onChange : function( event ) {

			var $target = this.$( event.currentTarget ),
			      field = $target.attr( 'data-field' ),
						value = $target.val();

			this.controller.trigger( 'contextmenu:filter', field, value );

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
			$( 'body' ).append( self.render().$el );

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
			this.remove();

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

			this.$el.addClass( 'active' );

			var position = this.controller.get( 'coordinates' ) || {},
			   overflowX = ( window.innerWidth <= ( position.x + 200 ) ),
			suboverflowX = ( window.innerWidth <= ( position.x + 400 ) ),
			   overflowY = ( window.innerHeight <= ( position.y + this.$el.height() ) );

			this.$el.css({
				left : ( overflowX ? ( position.x - this.$el.innerWidth() ) : position.x ) || 0,
				top  : ( overflowY ? ( position.y - this.$el.innerHeight() ) : position.y ) || 0,
			});

			this.$el.toggleClass( 'sub-menu-left', suboverflowX );
			this.$el.toggleClass( 'sub-menu-bottom', overflowY );

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

			return this;
		},

	});

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

			this.groups = new contextMenuGroups;

			//this.on( 'contextmenu:action', console.log );
			//this.on( 'contextmenu:filter', console.log );

			return this;
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
		 * Open the menu.
		 *
		 * @since 1.0.0
		 *
		 * @return Returns itself to allow chaining.
		 */
		open : function() {

			if ( this.menu ) {
				this.close();
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
		close : function() {

			if ( this.menu ) {
				this.menu.remove();
			}

			return this
		},

	});

})( jQuery, _, Backbone );

jQuery( document ).ready( function( $ ) {

	/*testcontextmenu = window.testcontextmenu = {};

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
					selectable : {
						field : 'field-a-b-a-a',
						value : 'MEH',
					},
				}, {
					id         : 'item-a-b-a-b',
					icon       : '',
					title      : 'Item A > B > A > B',
					selectable : {
						field : 'field-a-b-a-b',
						value : 'MEH',
					},
				},
			],
		});

		testcontextmenu.addGroup({
			id       : 'group-b',
			position : 0,
			items    : [
				{
					id       : 'item-b-a',
					icon     : 'dashicons dashicons-wordpress-alt',
					title    : 'Item B > A',
					position : 1,
				}, {
					id       : 'item-b-b',
					icon     : 'dashicons dashicons-wordpress-alt',
					title    : 'Item B > B',
					position : 0,
				},
			],
		});

		testcontextmenu.open();

	} );*/

} );
