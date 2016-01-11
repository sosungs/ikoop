/**
 * 选择权限
 */
Ext.define("PSI.Permission.SelectPermissionForm", {
	extend : "Ext.window.Window",

	config : {
		idList : null, // idList是数组
		parentForm : null
	},

	title : "选择权限",
	width : 700,
	height : 600,
	modal : true,
	resizable : false,
	layout : "border",

	initComponent : function() {
		var me = this;

		Ext.apply(me, {
			padding : 5,
			items : [{
						region : "center",
						layout : "border",
						border : 0,
						items : [{
									region : "north",
									layout : "border",
									height : "50%",
									border : 0,
									title: "所有可以选择的权限",
									items : [{
												region : "west",
												width : "30%",
												layout : "fit",
												border : 0,
												split: true,
												items : [me.getCategoryGrid()]
											}, {
												region : "center",
												border : 0,
												layout : "fit",
												items : [me.getPermissionGrid()]
											}]
								}, {
									region : "center",
									layout : "fit",
									border : 0,
									items : [me.getSelectedGrid()]
								}]
					}, {
						region : "south",
						layout : {
							type : "table",
							columns : 2
						},
						border : 0,
						height : 40,
						items : [{
									xtype : "textfield",
									fieldLabel : "数据域",
									margin : "5 5 5 5",
									labelWidth : 60,
									labelAlign : "right",
									labelSeparator : "",
									width : 480,
									readOnly : true,
									id : "editDataOrg"
								}, {
									xtype : "hidden",
									id : "editDataOrgIdList"
								}, {
									xtype : "button",
									text : "选择数据域",
									handler : me.onSelectDataOrg,
									scope : me
								}]
					}],
			buttons : [{
						text : "确定",
						formBind : true,
						iconCls : "PSI-button-ok",
						handler : this.onOK,
						scope : this
					}, {
						text : "取消",
						handler : function() {
							me.close();
						},
						scope : me
					}],
			listeners : {
				show : me.onWndShow
			}
		});

		me.callParent(arguments);
	},

	onWndShow : function() {
		var me = this;
		var idList = me.getIdList();
		var store = me.getPermissionGrid().getStore();

		var el = me.getEl() || Ext.getBody();
		el.mask("数据加载中...");
		var r = {
			url : PSI.Const.BASE_URL + "Home/Permission/selectPermission",
			params : {
				idList : idList.join()
			},
			method : "POST",
			callback : function(options, success, response) {
				store.removeAll();

				if (success) {
					var data = Ext.JSON.decode(response.responseText);

					store.add(data);
				}

				el.unmask();
			}
		};

		Ext.Ajax.request(r);
	},

	onOK : function() {
		var me = this;
		var grid = me.getPermissionGrid();

		var items = grid.getSelectionModel().getSelection();
		if (items == null || items.length == 0) {
			PSI.MsgBox.showInfo("没有选择权限");

			return;
		}

		var dataOrgList = Ext.getCmp("editDataOrgIdList").getValue();
		if (!dataOrgList) {
			PSI.MsgBox.showInfo("没有选择数据域");
			return;
		}

		if (me.getParentForm()) {
			var fullNameList = Ext.getCmp("editDataOrg").getValue();
			me.getParentForm().setSelectedPermission(items, dataOrgList,
					fullNameList);
		}

		me.close();
	},

	onSelectDataOrg : function() {
		var me = this;
		var form = Ext.create("PSI.Permission.SelectDataOrgForm", {
					parentForm : me
				});
		form.show();
	},

	setDataOrgList : function(fullNameList, dataOrgList) {
		Ext.getCmp("editDataOrg").setValue(fullNameList);
		Ext.getCmp("editDataOrgIdList").setValue(dataOrgList);
	},

	/**
	 * 所有可以选择的权限的Grid
	 */
	getPermissionGrid : function() {
		var me = this;
		if (me.__permissionGrid) {
			return me.__permissionGrid;
		}

		var modelName = "PSIPermission_SelectPermissionForm";
		Ext.define(modelName, {
					extend : "Ext.data.Model",
					fields : ["id", "name"]
				});

		var store = Ext.create("Ext.data.Store", {
					model : modelName,
					autoLoad : false,
					data : []
				});

		me.__permissionGrid = Ext.create("Ext.grid.Panel", {
					store : store,
					columns : [{
								header : "权限名称",
								dataIndex : "name",
								flex : 1,
								menuDisabled : true
							}]
				});

		return me.__permissionGrid;
	},

	/**
	 * 最终用户选择权限的Grid
	 */
	getSelectedGrid : function() {
		var me = this;
		if (me.__selectedGrid) {
			return me.__selectedGrid;
		}

		var modelName = "PSISelectedPermission_SelectPermissionForm";
		Ext.define(modelName, {
					extend : "Ext.data.Model",
					fields : ["id", "name"]
				});

		var store = Ext.create("Ext.data.Store", {
					model : modelName,
					autoLoad : false,
					data : []
				});

		me.__selectedGrid = Ext.create("Ext.grid.Panel", {
					title : "已经选择的权限",
					padding : 5,
					store : store,
					columns : [{
								header : "权限名称",
								dataIndex : "name",
								flex : 1,
								menuDisabled : true
							}]
				});

		return me.__selectedGrid;
	},

	/**
	 * 权限分类Grid
	 */
	getCategoryGrid : function() {
		var me = this;
		if (me.__categoryGrid) {
			return me.__categoryGrid;
		}

		var modelName = "PSIPermissionCategory_SelectPermissionForm";
		Ext.define(modelName, {
					extend : "Ext.data.Model",
					fields : ["name"]
				});

		var store = Ext.create("Ext.data.Store", {
					model : modelName,
					autoLoad : false,
					data : []
				});

		me.__categoryGrid = Ext.create("Ext.grid.Panel", {
					store : store,
					columns : [{
								header : "权限分类",
								dataIndex : "name",
								flex : 1,
								menuDisabled : true
							}]
				});

		return me.__categoryGrid;

	}
});