Ext.define("PSI.About.MainForm", {
    extend: 'Ext.window.Window',
    header: {
        title: "<span style='font-size:120%'>关于 - PSI</span>",
        iconCls: "PSI-fid-9994",
        height: 40
    },
    modal: true,
    closable: false,
    width: 400,
    layout: "fit",
    initComponent: function () {
        var me = this;

        Ext.apply(me, {
            height: 300,
            items: [{
                    border: 0,
                    xtype: "container",
                    html: "<h1>欢迎使用开源进销存PSI</h1><p>当前版本：" + PSI.Const.VERSION + "</p>"
                    + "<p>产品主页请点击这里：<a href='http://git.oschina.net/crm8000/PSI' target='_blank'>http://git.oschina.net/crm8000/PSI</a></p>"
                    + "<p>如需技术支持，请联系：</p><p>QQ：1569352868 Email：1569352868@qq.com QQ群：414474186</p>"
                }
            ],
            buttons: [{
                    id: "buttonOK",
                    text: "确定",
                    handler: me.onOK,
                    scope: me,
                    iconCls: "PSI-button-ok"
                }],
            listeners: {
                show: {
                    fn: me.onWndShow,
                    scope: me
                }
            }
        });

        me.callParent(arguments);
    },
    onWndShow: function () {
        Ext.getCmp("buttonOK").focus();
    },
    onOK: function () {
        this.close();
    }
});