// 采购入库 - 主界面
Ext.define("PSI.Purchase.PWMainForm", {
    extend: "Ext.panel.Panel",
    
    border: 0,
    
    layout: "border",

    initComponent: function () {
        var me = this;

        Ext.apply(me, {
            tbar: [
                {
                    text: "新建采购入库单", iconCls: "PSI-button-add", scope: me, handler: me.onAddBill
                }, "-", {
                    text: "编辑采购入库单", iconCls: "PSI-button-edit", scope: me, handler: me.onEditBill
                }, "-", {
                    text: "删除采购入库单", iconCls: "PSI-button-delete", scope: me, handler: me.onDeleteBill
                }, "-", {
                    text: "提交入库", iconCls: "PSI-button-commit", scope: me, handler: me.onCommit
                },"-",{
    				text : "帮助",
    				iconCls : "PSI-help",
    				handler : function() {
    					window.open("http://my.oschina.net/u/134395/blog/379622");
    				}
    			}, "-", {
                    text: "关闭", iconCls: "PSI-button-exit", handler: function () {
                        location.replace(PSI.Const.BASE_URL);
                    }
                }
            ],
            items: [{
                    region: "north", height: "30%",
                    split: true, layout: "fit", border: 0,
                    items: [me.getMainGrid()]
                }, {
                    region: "center", layout: "fit", border: 0,
                    items: [me.getDetailGrid()]
                }]
        });

        me.callParent(arguments);

        me.refreshMainGrid();
    },
    
    getMainGrid: function() {
    	var me = this;
    	if (me.__mainGrid) {
    		return me.__mainGrid;
    	}
    	
    	var modelName = "PSIPWBill";
        Ext.define(modelName, {
            extend: "Ext.data.Model",
            fields: ["id", "ref", "bizDate", "supplierName", "warehouseName", "inputUserName",
                "bizUserName", "billStatus", "amount"]
        });
        var store = Ext.create("Ext.data.Store", {
            autoLoad: false,
            model: modelName,
            data: [],
            pageSize: 20,
            proxy: {
                type: "ajax",
                actionMethods: {
                    read: "POST"
                },
                url: PSI.Const.BASE_URL + "Home/Purchase/pwbillList",
                reader: {
                    root: 'dataList',
                    totalProperty: 'totalCount'
                }
            }
        });
        store.on("load", function (e, records, successful) {
            if (successful) {
                me.gotoMainGridRecord(me.__lastId);
            }
        });


        me.__mainGrid = Ext.create("Ext.grid.Panel", {
        	viewConfig: {
                enableTextSelection: true
            },
            border: 0,
            columnLines: true,
            columns: [
                {xtype: "rownumberer", width: 50},
                {header: "状态", dataIndex: "billStatus", menuDisabled: true, sortable: false, width: 60,
                	renderer: function (value) {
                        return value == "待入库" ? "<span style='color:red'>" + value + "</span>" : value;
                    }
                },
                {header: "入库单号", dataIndex: "ref", width: 110, menuDisabled: true, sortable: false},
                {header: "业务日期", dataIndex: "bizDate", menuDisabled: true, sortable: false},
                {header: "供应商", dataIndex: "supplierName", width: 300, menuDisabled: true, sortable: false},
                {header: "采购金额", dataIndex: "amount", menuDisabled: true, sortable: false, align: "right", xtype: "numbercolumn", width: 150},
                {header: "入库仓库", dataIndex: "warehouseName", menuDisabled: true, sortable: false},
                {header: "业务员", dataIndex: "bizUserName", menuDisabled: true, sortable: false},
                {header: "录单人", dataIndex: "inputUserName", menuDisabled: true, sortable: false}
            ],
            store: store,
            tbar: [{
                    id: "pagingToobar",
                    xtype: "pagingtoolbar",
                    border: 0,
                    store: store
                }, "-", {
                    xtype: "displayfield",
                    value: "每页显示"
                }, {
                    id: "comboCountPerPage",
                    xtype: "combobox",
                    editable: false,
                    width: 60,
                    store: Ext.create("Ext.data.ArrayStore", {
                        fields: ["text"],
                        data: [["20"], ["50"], ["100"], ["300"], ["1000"]]
                    }),
                    value: 20,
                    listeners: {
                        change: {
                            fn: function () {
                                store.pageSize = Ext.getCmp("comboCountPerPage").getValue();
                                store.currentPage = 1;
                                Ext.getCmp("pagingToobar").doRefresh();
                            },
                            scope: me
                        }
                    }
                }, {
                    xtype: "displayfield",
                    value: "条记录"
                }],
            listeners: {
                select: {
                    fn: me.onMainGridSelect,
                    scope: me
                },
                itemdblclick: {
                    fn: me.onEditBill,
                    scope: me
                }
            }
        });
        
        return me.__mainGrid;
    },
    
    getDetailGrid: function() {
    	var me = this;
    	if (me.__detailGrid) {
    		return me.__detailGrid;
    	}
    	
    	var modelName = "PSIPWBillDetail";
        Ext.define(modelName, {
            extend: "Ext.data.Model",
            fields: ["id", "goodsCode", "goodsName", "goodsSpec", "unitName", "goodsCount",
                "goodsMoney", "goodsPrice"]
        });
        var store = Ext.create("Ext.data.Store", {
            autoLoad: false,
            model: modelName,
            data: []
        });

        me.__detailGrid = Ext.create("Ext.grid.Panel", {
            title: "采购入库单明细",
            viewConfig: {
                enableTextSelection: true
            },
            columnLines: true,
            columns: [
                Ext.create("Ext.grid.RowNumberer", {text: "序号", width: 30}),
                {header: "商品编码", dataIndex: "goodsCode", menuDisabled: true, sortable: false, width: 120},
                {header: "商品名称", dataIndex: "goodsName", menuDisabled: true, sortable: false, width: 200},
                {header: "规格型号", dataIndex: "goodsSpec", menuDisabled: true, sortable: false, width: 200},
                {header: "采购数量", dataIndex: "goodsCount", menuDisabled: true, sortable: false, align: "right"},
                {header: "单位", dataIndex: "unitName", menuDisabled: true, sortable: false, width: 60},
                {header: "采购单价", dataIndex: "goodsPrice", menuDisabled: true, sortable: false, align: "right", xtype: "numbercolumn", width: 150},
                {header: "采购金额", dataIndex: "goodsMoney", menuDisabled: true, sortable: false, align: "right", xtype: "numbercolumn", width: 150}
            ],
            store: store,
            listeners: {
                itemdblclick: {
                    fn: me.onEditPWBillDetail,
                    scope: me
                }
            }
        });

        return me.__detailGrid;
    },
    
    refreshMainGrid: function (id) {
    	var me = this;
    	
        var gridDetail = me.getDetailGrid();
        gridDetail.setTitle("采购入库单明细");
        gridDetail.getStore().removeAll();
        
        Ext.getCmp("pagingToobar").doRefresh();
        me.__lastId = id;
    },
    
    onAddBill: function () {
        var form = Ext.create("PSI.Purchase.PWEditForm", {
            parentForm: this
        });
        form.show();
    },
    
    onEditBill: function () {
    	var me = this;
        var item = me.getMainGrid().getSelectionModel().getSelection();
        if (item == null || item.length != 1) {
            PSI.MsgBox.showInfo("没有选择要编辑的采购入库单");
            return;
        }
        var bill = item[0];

        var form = Ext.create("PSI.Purchase.PWEditForm", {
            parentForm: me,
            entity: bill
        });
        form.show();
    },
    
    onDeleteBill: function () {
        var me = this;
        var item = me.getMainGrid().getSelectionModel().getSelection();
        if (item == null || item.length != 1) {
            PSI.MsgBox.showInfo("请选择要删除的采购入库单");
            return;
        }
        
        var bill = item[0];
        var store = me.getMainGrid().getStore();
        var index = store.findExact("id", bill.get("id"));
        index--;
        var preIndex = null;
        var preItem = store.getAt(index);
        if (preItem) {
            preIndex = preItem.get("id");
        }

        var info = "请确认是否删除采购入库单: <span style='color:red'>" + bill.get("ref") + "</span>";
        var me = this;
        PSI.MsgBox.confirm(info, function () {
            var el = Ext.getBody();
            el.mask("正在删除中...");
            Ext.Ajax.request({
                url: PSI.Const.BASE_URL + "Home/Purchase/deletePWBill",
                method: "POST",
                params: {id: bill.get("id")},
                callback: function (options, success, response) {
                    el.unmask();

                    if (success) {
                        var data = Ext.JSON.decode(response.responseText);
                        if (data.success) {
                            PSI.MsgBox.showInfo("成功完成删除操作", function () {
                                me.refreshMainGrid(preIndex);
                            });
                        } else {
                            PSI.MsgBox.showInfo(data.msg);
                        }
                    } else {
                        PSI.MsgBox.showInfo("网络错误", function () {
                            window.location.reload();
                        });
                    }
                }
            });
        });
    },
    
    onMainGridSelect: function () {
        this.refreshDetailGrid();
    },
    
    refreshDetailGrid: function (id) {
        var me = this;
        me.getDetailGrid().setTitle("采购入库单明细");
        var item = me.getMainGrid().getSelectionModel().getSelection();
        if (item == null || item.length != 1) {
            return;
        }
        var bill = item[0];

        var grid = me.getDetailGrid();
        grid.setTitle("单号: " + bill.get("ref") + " 供应商: " + bill.get("supplierName") 
        		+ " 入库仓库: " + bill.get("warehouseName"));
        var el = grid.getEl();
        el.mask(PSI.Const.LOADING);
        Ext.Ajax.request({
            url: PSI.Const.BASE_URL + "Home/Purchase/pwBillDetailList",
            params: {
            	pwBillId: bill.get("id")
            },
            method: "POST",
            callback: function (options, success, response) {
                var store = grid.getStore();

                store.removeAll();

                if (success) {
                    var data = Ext.JSON.decode(response.responseText);
                    store.add(data);

                    if (store.getCount() > 0) {
                        if (id) {
                            var r = store.findExact("id", id);
                            if (r != -1) {
                                grid.getSelectionModel().select(r);
                            }
                        }
                    }
                }

                el.unmask();
            }
        });
    },
    
    onCommit: function () {
    	var me = this;
        var item = me.getMainGrid().getSelectionModel().getSelection();
        if (item == null || item.length != 1) {
            PSI.MsgBox.showInfo("没有选择要提交的采购入库单");
            return;
        }
        var bill = item[0];

        var detailCount = me.getDetailGrid().getStore().getCount();
        if (detailCount == 0) {
            PSI.MsgBox.showInfo("当前采购入库单没有录入商品明细，不能提交");
            return;
        }

        var info = "请确认是否提交单号: <span style='color:red'>" + bill.get("ref") + "</span> 的采购入库单?";
        var id = bill.get("id");
        PSI.MsgBox.confirm(info, function () {
            var el = Ext.getBody();
            el.mask("正在提交中...");
            Ext.Ajax.request({
                url: PSI.Const.BASE_URL + "Home/Purchase/commitPWBill",
                method: "POST",
                params: {id: id},
                callback: function (options, success, response) {
                    el.unmask();

                    if (success) {
                        var data = Ext.JSON.decode(response.responseText);
                        if (data.success) {
                            PSI.MsgBox.showInfo("成功完成提交操作", function () {
                                me.refreshMainGrid(id);
                            });
                        } else {
                            PSI.MsgBox.showInfo(data.msg);
                        }
                    } else {
                        PSI.MsgBox.showInfo("网络错误", function () {
                            window.location.reload();
                        });
                    }
                }
            });
        });
    },
    
    gotoMainGridRecord: function (id) {
        var me = this;
        var grid = me.getMainGrid();
        grid.getSelectionModel().deselectAll();
        var store = grid.getStore();
        if (id) {
            var r = store.findExact("id", id);
            if (r != -1) {
                grid.getSelectionModel().select(r);
            } else {
                grid.getSelectionModel().select(0);
            }
        } else {
            grid.getSelectionModel().select(0);
        }
    }
});