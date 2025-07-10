/**
 * Created by jhkoo77 on 2015-09-10.
 */


var nxTSDTIParam = (function($){

    if (!Array.prototype.filter)
    {
        Array.prototype.filter = function(fun /*, thisp*/)
        {
            var len = this.length;
            if (typeof fun != "function")
                throw new TypeError();

            var res = new Array();
            var thisp = arguments[1];
            for (var i = 0; i < len; i++)
            {
                if (i in this)
                {
                    var val = this[i]; // in case fun mutates this
                    if (fun.call(thisp, val, i, this))
                        res.push(val);
                }
            }
            return res;
        };
    }

    function $args(func) {
        return (func+'').replace(/\s+/g,'')
            //.replace(/[/][*][^/*]*[*][/]/g,'') // strip simple comments
            .split('){',1)[0].replace(/^[^(]*[(]/,'') // extract the parameters
            .replace(/=[^,]+/g,'') // strip any ES6 defaults
            .split(',').filter(Boolean); // split & filter [""]
    }

    function argsToObj(names,args) {
        var obj = {};
        for(var i=0;i<names.length;i++) {
            obj[names[i]] = args[i];
        }
        return obj;
    };

    var _dtiparam = function(){
        this._paramData = {};

        this.getParam = function() {
            return this._paramData;
        };
        this.exchangedDocument = function(ID,ReferencedDocument,IssueDate) {
            nxTSLog.i("exchangedDocument");
            nxTSLog.d("exchangedDocument",arguments);
            this._paramData["exchangedDocument"] = argsToObj($args(arguments.callee),arguments);
        };
        this.taxInvoiceDocument = function(IssueID,IssueDateTime,TypeCode,PurposeCode,AmendmentStatusCode,DescriptionText) {
            nxTSLog.i("taxInvoiceDocument");
            nxTSLog.d("taxInvoiceDocument",arguments);
            this._paramData["taxInvoiceDocument"] = argsToObj($args(arguments.callee),arguments);
        };
        this.taxInvoiceDocumentV3 = function(IssueID,IssueDateTime,TypeCode,PurposeCode,AmendmentStatusCode,DescriptionText,OriginalIssueID) {
            nxTSLog.i("taxInvoiceDocumentV3");
            nxTSLog.d("taxInvoiceDocumentV3",arguments);
            this._paramData["taxInvoiceDocumentV3"] = argsToObj($args(arguments.callee),arguments);
        };
        this.addDescriptionTextClear = function() {
            nxTSLog.i("addDescriptionTextClear");
            nxTSLog.d("addDescriptionTextClear",arguments);
            this._paramData["addDescriptionText"] = [];
        };
        this.addDescriptionText = function(DescriptionText) {
            nxTSLog.i("addDescriptionText");
            nxTSLog.d("addDescriptionText",arguments);
            if(this._paramData.addDescriptionText == undefined)
                this._paramData["addDescriptionText"] = [];
            
            if(this._paramData.addDescriptionText.length >= 2 ) {
                alert("입력 개수를 초과 했습니다 [2개]");
                return;
            }

            this._paramData.addDescriptionText.push(argsToObj($args(arguments.callee),arguments));
        };
        this.originalIssueID = function(OriginalIssueID) {
            nxTSLog.i("originalIssueID");
            nxTSLog.d("originalIssueID",arguments);
            this._paramData["originalIssueID"] = argsToObj($args(arguments.callee),arguments);
        };
        this.invoicerParty = function(ID,Name,SpecifiedPersonName,TypeCode,ClassificationCode,TaxRegistrationID) {
            nxTSLog.i("invoicerParty");
            nxTSLog.d("invoicerParty",arguments);
            this._paramData["invoicerParty"] = argsToObj($args(arguments.callee),arguments);
        };
        this.invoicerPartyDefinedContact = function(DepartmentName,PersonName,Telephone,Email) {
            nxTSLog.i("invoicerPartyDefinedContact");
            nxTSLog.d("invoicerPartyDefinedContact",arguments);
            this._paramData["invoicerPartyDefinedContact"] = argsToObj($args(arguments.callee),arguments);
        };
        this.invoicerPartySpecifiedAddress = function(SpecifiedAddressLineOne) {
            nxTSLog.i("invoicerPartySpecifiedAddress");
            nxTSLog.d("invoicerPartySpecifiedAddress",arguments);
            this._paramData["invoicerPartySpecifiedAddress"] = argsToObj($args(arguments.callee),arguments);
        };
        this.invoiceeParty = function(ID,BusinessType,Name,SpecifiedPersonName,TypeCode,ClassificationCode,TaxRegistrationID) {
            nxTSLog.i("invoiceeParty");
            nxTSLog.d("invoiceeParty",arguments);
            this._paramData["invoiceeParty"] = argsToObj($args(arguments.callee),arguments);
        };
        this.invoiceePartySpecifiedAddress = function(SpecifiedAddressLineOne) {
            nxTSLog.i("invoiceePartySpecifiedAddress");
            nxTSLog.d("invoiceePartySpecifiedAddress",arguments);
            this._paramData["invoiceePartySpecifiedAddress"] = argsToObj($args(arguments.callee),arguments);
        };
        this.invoiceePartyPrimaryDefinedContact = function(DepartmentName,PersonName,Telephone,Email) {
            nxTSLog.i("invoiceePartyPrimaryDefinedContact");
            nxTSLog.d("invoiceePartyPrimaryDefinedContact",arguments);
            this._paramData["invoiceePartyPrimaryDefinedContact"] = argsToObj($args(arguments.callee),arguments);
        };
        this.invoiceePartySecondaryDefinedContact = function(DepartmentName,PersonName,Telephone,Email) {
            nxTSLog.i("invoiceePartySecondaryDefinedContact");
            nxTSLog.d("invoiceePartySecondaryDefinedContact",arguments);
            this._paramData["invoiceePartySecondaryDefinedContact"] = argsToObj($args(arguments.callee),arguments);
        };
        this.brokerParty = function(ID,Name,SpecifiedPersonName,TypeCode,ClassificationCode,TaxRegistrationID) {
            nxTSLog.i("brokerParty");
            nxTSLog.d("brokerParty",arguments);
            this._paramData["brokerParty"] = argsToObj($args(arguments.callee),arguments);
        };
        this.brokerPartyDefinedContact = function(DepartmentName,PersonName,Telephone,Email) {
            nxTSLog.i("brokerPartyDefinedContact");
            nxTSLog.d("brokerPartyDefinedContact",arguments);
            this._paramData["brokerPartyDefinedContact"] = argsToObj($args(arguments.callee),arguments);
        };
        this.brokerPartySpecifiedAddress = function(SpecifiedAddressLineOne) {
            nxTSLog.i("brokerPartySpecifiedAddress");
            nxTSLog.d("brokerPartySpecifiedAddress",arguments);
            this._paramData["brokerPartySpecifiedAddress"] = argsToObj($args(arguments.callee),arguments);
        };
        this.specifiedPaymentMeans = function(TypeCode,PaidAmount) {
            nxTSLog.i("specifiedPaymentMeans");
            nxTSLog.d("specifiedPaymentMeans",arguments);
            if(this._paramData.specifiedPaymentMeans == undefined) {
                this._paramData["specifiedPaymentMeans"] = [];
            }
            if(this._paramData.specifiedPaymentMeans.length > 4) {
                alert("입력 개수를 초과 했습니다 [4개]");
                return;
            }

            this._paramData.specifiedPaymentMeans.push(argsToObj($args(arguments.callee),arguments));
        };
        this.specifiedMonetarySummation = function(ChargeTotal,TaxTotal,GrandTotal) {
            nxTSLog.i("specifiedMonetarySummation");
            nxTSLog.d("specifiedMonetarySummation",arguments);
            this._paramData["specifiedMonetarySummation"] = argsToObj($args(arguments.callee),arguments);
        };
        this.tradeLineItemClear = function() {
            nxTSLog.i("tradeLineItemClear");
            nxTSLog.d("tradeLineItemClear",arguments);
            this._paramData["tradeLineItem"] = [];
        };
        this.tradeLineItem = function(Sequence,PurchaseExpiry,Name,Information,Description,ChargeableUnit,UnitPrice,InvoiceAmount,TotalTax) {
            nxTSLog.i("tradeLineItem");
            nxTSLog.d("tradeLineItem",arguments);
            if(this._paramData.tradeLineItem == undefined) {
                this._paramData["tradeLineItem"] = [];
            }

            if(this._paramData.tradeLineItem.length > 99) {
                alert("입력 개수를 초과 했습니다 [99개]");
                return;
            }

            this._paramData.tradeLineItem.push(argsToObj($args(arguments.callee),arguments))
        };
    };

    return _dtiparam;
})(jQuery);

var nxTSDTIObj = (function(parentObj,$){
    var _dti = {};
    var _moduleName = nxTSConfig.TSXMLTOOLKIT;

    _dti = $.extend({},parentObj);

    _dti._getResultTimer = new nxTSUtil.timer(nxTSConfig.getResultInterval,function(){
        nxTSDTIObj.getResult(nxts_get_result_complete_callback).invoke();
    });

    _dti.createCommand = function(cmd,data,ctx) {
        ctx = ctx || {};
        ctx.obj = nxTSDTIObj;
        var cmd =  nxTSCommon.createCommand(_moduleName,cmd,data,ctx);
        cmd.data.async = true;
        return cmd;
    };

    _dti.sign = function(type,taxInvoice,options,callback,etc) {
        options = $.extend({},nxTSDTIConfig.options,options);
        var cmd = this.createCommand("sign",{options:options,type:type,taxInvoice:taxInvoice},{callback:callback,etc:etc});
        this.startGetResultTimer(cmd.data.rid,etc);
        return cmd;
    };
    _dti.verify = function(signedTaxInvoice,options,callback,etc) {
        options = $.extend({},nxTSDTIConfig.options,options);
        var cmd = this.createCommand("verify",{options:options,signedTaxInvoice:signedTaxInvoice},{callback:callback,etc:etc});
        this.startGetResultTimer(cmd.data.rid,etc);
        return cmd;
    };
    _dti.generateTaxInvoiceXML = function(param,options,callback,etc) {
        options = $.extend({},nxTSDTIConfig.options,options);
        var cmd = this.createCommand("generateTaxInvoiceXML",{options:options,param:param},{callback:callback,etc:etc});
        this.startGetResultTimer(cmd.data.rid,etc);
        return cmd;
    };
    _dti.checkRValue = function(RValue,signedTaxInvoice,callback,etc) {
        var cmd = this.createCommand("checkRValue",{RValue:RValue,signedTaxInvoice:signedTaxInvoice},{callback:callback,etc:etc});
        this.startGetResultTimer(cmd.data.rid,etc);
        return cmd;
    };
    _dti.versionInfo = function(callback,etc) {
        var cmd = this.createCommand("versionInfo",{},{callback:callback,etc:etc});
        this.startGetResultTimer(cmd.data.rid,etc);
        return cmd;
    };
    _dti.getResult  = function(callback) {
        var cmd = this.createCommand("getResult",{ajaxto:nxTSConfig.getResultTimeout},{callback:callback});
        cmd.data.async = false;
        return cmd;
    };

    return _dti;
})(nxTSCommonObj,jQuery);

var nxTSDTI = (function(parentObj,$){
    var _dti = {};


    var _parentObj = parentObj;

    var _proxyFunctionNames = [
        "sign",
        "verify",
        "checkRValue",
        "versionInfo",
        "generateTaxInvoiceXML"
    ];


    _dti = $.extend(_parentObj,_dti);

    nxTSCommon.backupOrgFunctions(parentObj,_proxyFunctionNames);
    nxTSCommon.createProxyFunctions(parentObj,_dti,"callWithInit",_proxyFunctionNames);


    _dti.callWithInit = function(name,fn,args) {

        var initSuccess = function(res,ctx) {
            if(res == undefined || res.code != nxTSError.res_success) {
                nxTSUtil.showError(res);
                return;
            }

            nxTSLog.i(name.replace("_par_",""));
            //nxTSLog.d(name.replace("_par_",""),nxTSUtil.maskPassword(args));
            var cmd =fn.apply(parentObj,args);
            nxTSLog.d(name.replace("_par_",""),nxTSUtil.maskPassword(cmd.data.data));

            cmd.invoke();
        };

        if(nxTSSession.isInit() == false)
            nxTSCommon.init({versionCheck:[nxTSConfig.TSXMLTOOLKIT],
                installMessage:nxTSDTIConfig.installMessage,
                installPage:nxTSDTIConfig.installPage},initSuccess);
        else
            initSuccess({code:nxTSError.res_success});

    };


    _dti.installCheck = function() {
        setTimeout(function(){
            nxTSCommon.installCheck(false,{ajaxto:3000,success:function(res,data){
                if(res.code != nxTSError.res_success) {
                    if(confirm(nxTSDTIConfig.installMessage) == true) {
                        window.location.href = nxTSDTIConfig.installPage;
                    }
                }
            },versionCheck:[nxTSConfig.TSXMLTOOLKIT]});
        },500);
    };

    _dti.init = function(transparentDisableBrowser,disableBrowser,fn) {
        if(typeof disableBrowser === "function") {
            fn = disableBrowser;
            disableBrowser = true;
        }
        disableBrowser              = (typeof disableBrowser === 'boolean') ? disableBrowser :  true;
        transparentDisableBrowser   = (typeof transparentDisableBrowser === 'boolean') ? transparentDisableBrowser : false;

        if(fn != undefined) {
            nxTSDTI.onInit(fn);
        }

        setTimeout(function(){
            if(nxTSSession.isInit() == false) {
                nxTSCommon.init({
                        transparentDisableBrowser:transparentDisableBrowser,
                        disableBrowser:disableBrowser,
                        versionCheck:[nxTSConfig.TSXMLTOOLKIT],
                        installMessage:nxTSDTIConfig.installMessage,
                        installPage:nxTSDTIConfig.installPage},
                        function(res){
                            if(res.code == nxTSError.res_success)
                                nxTSDTI.callPostInit();
                        });
            }
            else {
                nxTSDTI.callPostInit();
            }

        },500);
    };

    nxTSCommon.updateConfig(nxTSDTIConfig);
    return _dti;
})(nxTSDTIObj,jQuery);

/*
function customDisableBrowser() {   alert("disable"); }
function customEnableBrowser() {    alert("enable"); }

nxTS_disableBrowser = customDisableBrowser;
nxTS_enableBrowser = customEnableBrowser;

*/

