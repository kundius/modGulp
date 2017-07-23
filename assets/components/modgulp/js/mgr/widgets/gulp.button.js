var modGulp = function(config) {
	config = config || {};

	var tplButton = new Ext.Template('<span id="{4}" unselectable="on"><button type="{0}"></button></span>');

	this.btnLog = new Ext.Button({
		text: '<i class="icon icon-square icon-stack-2x"></i><i class="icon icon-terminal icon-stack-1x"></i>',
		iconCls: 'modgulp-button icon-stack',
		template: tplButton,
		handler: this.showLog,
		scope: this
	});

	this.btnStart = new Ext.Button({
		text: '<i class="icon icon-square icon-stack-2x"></i><i class="icon icon-play icon-stack-1x"></i>',
		iconCls: 'modgulp-button icon-stack',
		template: tplButton,
		hidden: config.isActive,
		handler: this.start,
		scope: this
	});

	this.btnStop = new Ext.Button({
		text: '<i class="icon icon-square icon-stack-2x"></i><i class="icon icon-stop icon-stack-1x"></i>',
		iconCls: 'modgulp-button icon-stack',
		template: tplButton,
		hidden: !config.isActive,
		handler: this.stop,
		scope: this
	});

	Ext.applyIf(config,{
		style: {
			marginTop: '10px'
		},
	    tbar: [{
	        tag: 'img',
	        height: 24,
	        style: {
	            "margin-right": '5px'
	        },
	        src: config.imagesUrl + 'gulp-logo.svg'
	    }, this.btnStart, this.btnStop, this.btnLog]
	});

	modGulp.superclass.constructor.call(this,config);
};
Ext.extend(modGulp,Ext.Panel,{
	start: function() {
		MODx.Ajax.request({
			url: this.connectorUrl,
			params: {
				action: 'mgr/start'
			},
			listeners: {
				success: {fn: function(response) {
					this.btnStart.hide();
					this.btnStop.show();
					this.isActive = true;
				}, scope: this},
				failure: {fn: function(response) {
					Ext.Msg.alert(_('error'), response.message);
				}, scope: this}
			}
		});
	},

	stop: function() {
		MODx.Ajax.request({
			url: this.connectorUrl,
			params: {
				action: 'mgr/stop',
			},
			listeners: {
				success: {fn: function(response) {
					this.btnStart.show();
					this.btnStop.hide();
					this.isActive = false;
				}, scope: this},
				failure: {fn: function(response) {
					Ext.Msg.alert(_('error'), response.message);
				}, scope: this}
			}
		});
	},

	showLog: function() {
		var self = this;

		if (!self.logWindow) {
			self.logWindow = new MODx.Window({
				id: "modgulp-window",
				title: _("modgulp_log"),
				height: 500,
				width: 800,
				cloaseAction: 'hide',
				padding: 10,
				items: [{
					xtype: "textarea",
					id: "modgulp-log-content",
					name: "log",
					fieldLabel: "Log",
					value: '',
					readOnly: true,
					height: "100%",
					width: "100%",
					style: {
						boxSizing: "border-box"
					}
				}],
				buttons: [{
					text: _("update"),
					id: "modgulp-update-btn",
					cls: 'primary-button',
					handler: function (w) {
						Ext.get('modgulp-update-loading').show();
						self.logWindow._update();
					}
				}, {
					text: _("cancel"),
					handler: function (w) {
						self.logWindow.hide();
					}
				} , {
					tag: 'div',
					cls: 'loading-indicator',
					style: {
						position: 'absolute',
						top: 0,
						bottom: '46px',
						left: 0,
						right: 0,
						"background-position": '50% 50%',
						"background-color": 'rgba(255,255,255,.6)'
					},
					id: 'modgulp-update-loading'
				}],
				listeners: {
					beforeshow: function() {
						self.logWindow._update();
					},
					show: function() {
						this._interval = setInterval(function() {
							if(self.isActive) {
								self.logWindow._update();
							}
						}, 5000);
					},
					hide: function() {
						clearInterval(this._interval);
					}
				},
				_update: function(){
					MODx.Ajax.request({
						url: self.connectorUrl,
						params: {
							action: 'mgr/status',
						},
						listeners: {
							success: {fn: function(response) {
								var log = response.message.log !== false ? response.message.log : "",
										textarea = Ext.ComponentMgr.get('modgulp-log-content');

								textarea.setValue(log);
								textarea.el.scroll('bottom', textarea.el.dom.scrollHeight);

								Ext.get('modgulp-update-loading').hide();
							}, scope: this},
							failure: {fn: function(response) {
								Ext.get('modgulp-update-loading').hide();
								Ext.Msg.alert(_('error'), response.message);
							}, scope: this}
						}
					});
				}
			});
		}
		this.logWindow.show(Ext.EventObject.target);
	}
});
Ext.reg('modgulp',modGulp);

Ext.onReady(function() {
	var menu = document.getElementById("modx-user-menu"),
		li = document.createElement("LI");

	menu.insertBefore(li, menu.firstChild);

	Ext.onReady(function() {
        MODx.load({
        	xtype: "modgulp",
        	renderTo: li,
        	imagesUrl: modGulpConfig.imagesUrl,
        	isActive: modGulpConfig.isActive,
        	connectorUrl: modGulpConfig.connectorUrl
        });
    });
});
