/**
 * Created by jhkoo77 on 2015-09-10.
 */

var nxTSDTIConfig = {version:{},options:{}};

nxTSDTIConfig.version.nx             = "1,0,1,4";
nxTSDTIConfig.version.tsxmltoolkit   = "3,0,1,0";
nxTSDTIConfig.installPage            = "/NXTSDemo/NXTSDemo_1.0.1.4/install/dti/";
nxTSDTIConfig.installMessage         = "SCORE DTI for OpenWeb 프로그램이 설치 되어 있지 않거나, 이전 버전이 설치되어 있습니다. \n\n설치페이지로 이동하시겠습니까?";
nxTSDTIConfig.processingImageUrl     = "/NXTSDemo/NXTSDemo_1.0.1.4/img/processing2.gif"


nxTSDTIConfig.options.policySet = "1 2 410 200012 1 1 61:TradeSign관리자|1 2 410 200004 5 2 1 5 12:정보특목용|1 2 410 200012 1 1 3:범용기업|1 2 410 200004 5 1 1 7:범용기업|1 2 410 200005 1 1 5:범용기업|1 2 410 200004 5 2 1 1:범용기업|1 2 410 200004 5 4 1 2:범용기업|1 2 410 200004 5 3 1 1:범용기관|1 2 410 200004 5 3 1 2:범용기업|1 2 410 200005 1 1 6 8:국세청신고용";
//nxTSDTIConfig.options.policySet = "";
nxTSDTIConfig.options.storageType = -1;


nxTSMessage.iframeTimeout = "응답이 지연되었습니다 잠시후 다시 시도해주세요..";
nxTSMessage.ajaxTimeout = "응답이 지연되었습니다 잠시후 다시 시도해주세요.";
