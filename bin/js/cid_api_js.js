//�ؽ�Ʈ �ڽ� �ʱ�ȭ 
var keycnt = 0;
var idcnt = 0;
var passcnt = 0;

function keyclear(){
if (keycnt == 0)
{
	document.all.authkey.value = "";
}
keycnt =1;
}

function idclear(){
if (idcnt == 0)
{
	document.all.loginID.value = "";
}
idcnt = 1;
}

function passclear(){
if (passcnt == 0)
{
	document.all.loginPWD.value = "";
}
passcnt = 1;
}

//OPEN_API ���� Ȯ�� �Լ�
function getver(){
	document.getElementById("verBox").innerText = KTOpenAPIX.GetApiVer();
}


//�α��� ��û �Լ�
function Login(){
	var	result		=	500;
	var Server			=	document.all.server.value;
	var Authkey		=	document.all.authkey.value;
	var LoginID		=	document.all.loginID.value;
	var LoginPWD	=	document.all.loginPWD.value;
	var date = new Date();
	
		result		=	KTOpenAPIX.Login( Server, Authkey, LoginID, LoginPWD );
		switch (result)
		{
		case 200:break;
		case 301:alert("CID �ٸ� ��ġ���� �α���");break;
		case 401:alert("CID �̵�� ���̵�� �α���"); break;
		case 402:alert("CID ��й�ȣ ���� Ƚ�� �ʰ� (5ȸ����)"); break;
		case 403: alert("CID �ӽú��ȣ �α���");break;
		case 404:alert("CID �ӽú�й�ȣ ����"); break;
		case 405:alert("CID ��й�ȣ ����"); break;
		case 407:alert("CID ���� IP ����"); break;
		case 408: alert("CID �̵�� PC");break;
		case 500: alert("CID ��Ÿ(HTTPS/HTTP ��û ����)");break;
		case 1000:alert("CID �̹� �α�����"); break;
		case 1001:alert("CID ���� Ÿ�� ����"); break;
		case 1502:alert("CID ���� �������� ������"); break;
		case 1503: alert("CID ����Ű ��ȿ�Ⱓ�� ������");break;
		case 1504:alert("CID ����Ű ��Ȱ��"); break;
		case 1505: alert("CID ����Ű Ÿ���� Ʋ�� ���");break;
		case 1506: alert("CID ���� �����̳� ��� ����Ű, ��� Flag�� ���");break;
		case 1507: alert("CID ��� �����̳� ���� ����Ű, ���� Flag�� ���");break;
		case 1700: alert("CID API ȯ�� ���� ���� ����(����Ǵ� ���)");break;
		case 1701: alert("CID KTA_API.dat / KTD_API.dat���� data ���� �ʱ�ȭ ���� ������ �����ؾ� ��"); break;	 
		case 1702: alert("CID PC �޸� ����(API ���� ����)");break;

		default: alert(result);
		}
		
	
		/*
		date.setMinutes( date.getMinutes() + 20 );
		var year = 	date.getFullYear();
		var month= date.getMonth()+1;
			month = (month < 10)?"0" + month:month;		
		var day = (date.getDate() < 10)?"0" + date.getDate():date.getDate();
		var hour =  (date.getHours() < 10)?"0" + date.getHours():date.getHours();
		var minute =  date.getMinutes();
			minute = (minute < 10)?"0" + minute:minute;
		var time = year +""+ month +"" + day+ "" + hour+ "" + minute;
		document.getElementById("sSendDate").value =time;
		*/
			
}

//�α׾ƿ� ��û �Լ�

function  Logout(){
  var result = 500;
	result = confirm("�α׾ƿ� �Ͻðڽ��ϱ�?");
	 if (result == true)
	 {
		 KTOpenAPIX.Logout();
	 } 
}

//���� ����� ���� �α׾ƿ� ��û �Լ�
function LoginKickOut(){
	var re = confirm("�����α׾ƿ� �Ͻðڽ��ϱ�?");
	var result = 500;
	if (re == true)
	{
		 result = KTOpenAPIX.LoginKickOut();
		 if (result == "0")
		{
			alert("�ߺ��α��� ���°� �ƴ�");
		}else {
			alert(result)
		}
	}
	
}

//ȸ��û�� ��û �Լ�
function  LineJoin(){
	var Authkey		=	document.all.authkey.value;
	KTOpenAPIX.LineJoin(Authkey);
}
// ���� Ȯ�� �Լ�
function HelpEX(){
	var Server			=	document.all.server.value;
	KTOpenAPIX.HelpEX(Server);
}

//ȸ�� ���� Ȯ�� �ռ�
function UserJoinEX(){
var Server			=	document.all.server.value;
	KTOpenAPIX.UserJoinEx(Server)
}
// �н����� ã�� Ȯ�� �Լ�
function FindPasswdEX(){
	var Server			=	document.all.server.value;
	KTOpenAPIX.FindPasswdEx(Server)
}
// ���� ���� �Լ�
function SetMyInfo(){
	KTOpenAPIX.SetMyInfo()
}

//���� ��ȭ��ȣ ����Ʈ ���� �� ���� �� ���ϱ� �Լ�

function GetPhoneList(){

	var Ctc			=	"";
	var Intercall	=	"";
	var Tollline		=	"";
	var Localcall	=	"";
	var Mobile		=	"";
	var Cid			=	"";
	var Smssend	=	"";
	var Smsrecv	=	"";
	var Mainnum	=	"";
	var LineState	=	"";
	var GetPhoneList = "";
	
	
	KTDPhone = KTOpenAPIX.GetPhoneList()
if( KTDPhone != null && KTDPhone != undefined )
		{
	var KTDPhoneArray	=	( new VBArray( KTDPhone ) ).toArray();
	
		for( i = 0; i < KTDPhoneArray.length; i++ )
				{
					if (KTDPhoneArray[ i ].Ctc == "1"){
							Ctc = "O"; 
						}else{
							Ctc = "X";
						}
					if (KTDPhoneArray[ i ].Intercall == "1"){
							Intercall = "O"; 
						}else{
							Intercall = "X";
						}		
					if (KTDPhoneArray[ i ].Tollline == "1"){
							Tollline = "O"; 
						}else{
							Tollline = "X";
						}
					if (KTDPhoneArray[ i ].Localcall == "1"){
							Localcall = "O"; 
						}else{
							Localcall = "X";
						}	
					if (KTDPhoneArray[ i ].Mobile == "1"){
							Mobile = "O"; 
						}else{
							Mobile = "X";
						}		
					if (KTDPhoneArray[ i ].Cid == "1"){
							Cid = "O"; 
						}else{
							Cid = "X";
						}		
					if (KTDPhoneArray[ i ].Smssend == "1"){
							Smssend = "O"; 
						}else{
							Smssend = "X";
						}		
					if (KTDPhoneArray[ i ].Smsrecv == "1"){
							Smsrecv = "O"; 
						}else{
							Smsrecv = "X";
						}		
					if (KTDPhoneArray[ i ].Mainnum == "1"){
							Mainnum = "O"; 
						}else{
							Mainnum = "X";
						}		
					if (KTDPhoneArray[ i ].LineState == "0"){
							LineState = "������ȭ"; 
						}else if(KTDPhoneArray[ i ].LineState == "1"){
							LineState = "������ȯ";
						}else if(KTDPhoneArray[ i ].LineState == "2"){
							LineState = "������ ������ȯ";
						}else if(KTDPhoneArray[ i ].LineState == "3"){
							LineState = "������ ��Ʈ";
						}
					
					GetPhoneList += "ȸ����ȣ : "			+  KTDPhoneArray[ i ].Telnum		+
						", ��ȭ �߽� : "	+	Ctc			+  
						", ������ȭ : "		+	Intercall		+
						", ��ȸ��ȭ : "		+	Tollline		+
						", �ó���ȭ : "		+	Localcall	+
						", �޴���ȭ : "		+	Mobile		+
						", �߽����� ǥ�� : "		+ Cid		+
						", SMS�߽� : "	+	Smssend	+
						", SMS���� : "	+	Smsrecv	+
						", ��ǥ��ȣ : "		+	Mainnum	+
						", ȸ������ : "	 	+	LineState	+
						", ������ȯ : "		+	KTDPhoneArray[ i ].RecvTel		+  "\n"; 
						
				}				
					
		}
		document.getElementById("GetPhoneList").value = GetPhoneList;
}


//���� ���� ��ȭ��ȣ ����Ʈ ���� �� ���� �� ���ϱ� �Լ�

function GetMobilePhoneList()
{

	var MobilePhoneInfo = "";

	var KTDMPhoneList = KTOpenAPIX.GetMobilePhoneList()


	

	if( KTDMPhoneList != null && KTDMPhoneList != undefined )
	{
		var KTDPhoneArray	=	( new VBArray( KTDMPhoneList ) ).toArray();

		for( i = 0; i < KTDPhoneArray.length; i++ )
		{

			MobilePhoneInfo += "���� ��ȭ��ȣ : "  + KTDPhoneArray[i].MobilePhoneNum  +
							", CID���� ���Կ��� : " +  KTDPhoneArray[i].MobileCID  +
							", ���󼭺� ���� : " +  KTDPhoneArray[i].MobileStatus + "\n";
		}
	}
	else
	{
		MobilePhoneInfo = "���Ե� ���� ��ȭ��ȣ�� ����	";
	}
	document.getElementById("GetMobilePhoneList").value = MobilePhoneInfo;
}


//ȸ���� ��ȭ ����(������ȭ/������ȯ/������ ������ȯ) ���� �Լ�
function SetLineState(){
	var result = 500;
	var sTelNum = document.all.sTelNum.value;
	var sTelState = document.all.sTelState.value;
	var sRecvTel = document.all.sRecvTel.value;
	
	result = KTOpenAPIX.SetLineState( sTelNum, sTelState, sRecvTel);

	switch (result)
	{
	case 0: alert("��Ÿ ����"); break;
	case 200: alert("������ ���� ��û ������"); break;
	case 4002: alert("��밡�� ȸ���� �ƴ�"); break;
	case 4110: alert("��ȭ ���� ���� ���� ����"); break;
	case 4111: alert("��ȭ ���� ��ȣ�� �߸���(sTelState Ȯ��)"); break;
	case 1600: alert("��Ʈ�� ��û ����"); break;
	case 1601: alert("��Ʈ�� ��û ����"); break;
	default:alert(result);
	}
	
}


//��ȭ��ȣ ���� �Լ�

function SetRecvPhone(){
	var	result		=	500;
	var sRecvPhone = document.all.sRecvPhone.value;
		result = KTOpenAPIX.SetRecvPhone(sRecvPhone)
		
		switch (result)
		{
		case 0: alert("��Ÿ����"); break;
		case 200: alert("������ ��û ������"); break;
		case 2000: alert("�α��� �Ǿ� ���� ����"); break;
		case 3001: alert("���Ź�ȣ ����(�ڸ��� �ּ� 8 �ڸ�)"); break;
		case 4005: alert("���Ź�ȣ ����"); break;
		case 4004: alert("���Ź�ȣ ���� ����(�ִ�32��)"); break;
		default : alert(result);
		}
		GetRecvPhone();	
}

//���õ� ��ȭ��ȣ �������� �Լ�
function GetRecvPhone(){
	var RecvPhonelist ="";
	
	GetPhone = KTOpenAPIX.GetRecvPhone()

	if( GetPhone != null && GetPhone != undefined )
			{
		var GetPhoneArray 	=	( new VBArray( GetPhone ) ).toArray();
		
		for( i = 0; i < GetPhoneArray.length; i++ )
					{
					RecvPhonelist += GetPhoneArray[ i ].Callee +  "\n";
					}
			}else	{
					RecvPhonelist		+=	"ã�� �� ����";
					}
		document.getElementById("RecvPhonelist").value =RecvPhonelist;	
}

//���õ� ��ȭ��ȣ ���� �Լ�
function RemoveRecvPhone(){
	var	result		=	500;
		var sRecvPhone = document.all.sRecvPhone.value;
		result	=	KTOpenAPIX.RemoveRecvPhone(sRecvPhone);
		switch (result)
		{
		case 0: alert("��Ÿ����"); break;
		case 200: alert("������ ��û ������");break;
		case 2000: alert("�α��� �Ǿ� ���� ����"); break;
		case 4001: alert("��ȭ��ȣ �������� ����"); break;
		default: alert(result);
		}
		GetRecvPhone();	
}

//���õ� ��ȭ��ȣ ��ü���� �Լ�
function RemoveAllRecvPhone(){
	var	result		=	500;
		result	=	KTOpenAPIX.RemoveAllRecvPhone();
		switch (result)
		{
		case 0: alert("��Ÿ ����"); break;
		case 200: alert("������ ��û ������"); break;
		case 2000: alert("�α��� �Ǿ� ���� ����"); break;
		default : alert(result);
		}
		GetRecvPhone();	
}

//��ȭ�ɱ� ��û �Լ�
function SendCTC(){
	var	result		=	500;
		var sCaller		=	document.all.sCaller.value;
		var sCallee	=	document.all.sCallee.value;
		result	=	KTOpenAPIX.SendCTC(sCaller, sCallee);
		
		switch (result)
		{
		case 200: alert("������ ��û������"); break;
		case 2000: alert("�α��� �Ǿ����� ����"); break;
		case 3000: alert("����/�߽� ��ȣ�� ����"); break;
		case 3001: alert("���Ź�ȣ ����(�ڸ��� �ּ� 8�ڸ�)"); break;
		case 4002: alert("��ȭ�ɱ⸦ �� �� �ִ� ��ȭ��ȣ�� �ƴ�"); break;
		case 4101: alert("�ڵ����� ��ȭ�� �� ������ ����"); break;
		case 4102: alert("������ȭ�� �� ������ ����"); break;
		case 4103: alert("�ó���ȭ�� �� ������ ����"); break;
		case 4104: alert("�ÿ���ȭ�� �� ������ ����"); break;
		default : alert(result);
		}

}
//������ȭ ��û �Լ�
function SendCON(){
	var result = 500;
	var sCaller	 =	document.all.sCaller.value;
	var sRecvPhone	 =	document.all.sRecvPhone.value;
		KTOpenAPIX.SetRecvPhone( sRecvPhone );
		result = KTOpenAPIX.SendCON(sCaller);
	alert(result);
	switch (result)
		{
		case 200: alert("������ ��û������"); break;
		case 2000: alert("�α��� �Ǿ����� ����"); break;
		case 3000: alert("����/�߽� ��ȣ�� ����"); break;
		case 3001: alert("���Ź�ȣ ����(�ڸ��� �ּ� 8�ڸ�)"); break;
		case 4002: alert("��ȭ�ɱ⸦ �� �� �ִ� ��ȭ��ȣ�� �ƴ�"); break;
		case 4101: alert("�ڵ����� ��ȭ�� �� ������ ����"); break;
		case 4102: alert("������ȭ�� �� ������ ����"); break;
		case 4103: alert("�ó���ȭ�� �� ������ ����"); break;
		case 4104: alert("�ÿ���ȭ�� �� ������ ����"); break;
		default : alert(result);
		}
}

//������ȭ ����� �߰� �Լ�
function SendCONAddUser()
	{
		var	result		=	500;
		var sCaller		=	document.all.sCaller.value;
		var sCallee	=	document.all.sCallee.value;
		var sDBID		=	document.all.sDBID.value;
		result = KTOpenAPIX.SendCONAddUser(sDBID, sCaller, sCallee ); 
		switch (result)
		{
		case 200: alert("������ ��û������"); break;
		case 2000: alert("�α��� �Ǿ����� ����"); break;
		case 3000: alert("����/�߽� ��ȣ�� ����"); break;
		case 3001: alert("���Ź�ȣ ����(�ڸ��� �ּ� 8�ڸ�)"); break;
		case 4002: alert("��ȭ�ɱ⸦ �� �� �ִ� ��ȭ��ȣ�� �ƴ�"); break;
		case 4101: alert("�ڵ����� ��ȭ�� �� ������ ����"); break;
		case 4102: alert("������ȭ�� �� ������ ����"); break;
		case 4103: alert("�ó���ȭ�� �� ������ ����"); break;
		case 4104: alert("�ÿ���ȭ�� �� ������ ����"); break;
		default : alert(result);
		}

	}
//������ȭ ����� ���� �Լ�
function SendCONKickOutUser(){
		var	result		=	500;
		var sCaller		=	document.all.sCaller.value;
		var sCallee	=	document.all.sCallee.value;
		var sDBID		=	document.all.sDBID.value;
		result = KTOpenAPIX.SendCONKickOutUser(sDBID, sCaller, sCallee ); 
		switch (result)
		{
		case 200: alert("������ ��û������"); break;
		case 2000: alert("�α��� �Ǿ����� ����"); break;
		case 3000: alert("����/�߽� ��ȣ�� ����"); break;
		case 3001: alert("���Ź�ȣ ����(�ڸ��� �ּ� 8�ڸ�)"); break;
		case 4002: alert("��ȭ�ɱ⸦ �� �� �ִ� ��ȭ��ȣ�� �ƴ�"); break;
		case 4101: alert("�ڵ����� ��ȭ�� �� ������ ����"); break;
		case 4102: alert("������ȭ�� �� ������ ����"); break;
		case 4103: alert("�ó���ȭ�� �� ������ ����"); break;
		case 4104: alert("�ÿ���ȭ�� �� ������ ����"); break;
		default : alert(result);
		}
}
//���� ���ۿ�û �Լ�
function SendSMS(){
		var	result		=	500;
		var MsCaller		=	document.all.MsCaller.value;
		var sDisplay	=	document.all.sDisplay.value;
		var sMessage		=	document.all.sMessage.value;

	result = KTOpenAPIX.SendSMS( MsCaller, sDisplay, sMessage);
		switch(result){
		case 0: alert("���� ��û ����"); break;
		case 200: alert("������ ��û ������"); break;
		case 2000: alert("�α��� �Ǿ� ���� ����"); break;
		case 4001: alert("���Ź�ȣ ����"); break;
		case 4003: alert("�޼��� ����"); break;
		case 4004: alert("SMS �߽� �ѵ� �ʰ�"); break;
		case 4005: alert("�߽Ź�ȣ ����"); break;
		case 4006: alert("�޼��� ���� ����(�ִ� 80bytes)"); break;
		default : alert(result);
	}
}

// ���� ���� ���� �Լ� 
function SendReserveSMS(){
		var	result		=	500;
		var sRecvPhone = document.all.sRecvPhone.value;
		var MsCaller		=	document.all.MsCaller.value;
		var sDisplay		=	document.all.sDisplay.value;
		var sMessage		=	document.all.sMessage.value;
		var sSendDate		=	document.all.sSendDate.value;
		var ReserveSMS	 ="";
		var list		= "";
	
	result = KTOpenAPIX.SendReserveSMS( MsCaller, sDisplay, sMessage, sSendDate);
	
	GetPhone = KTOpenAPIX.GetRecvPhone();
	if( GetPhone != null && GetPhone != undefined )
			{
		var GetPhoneArray 	=	( new VBArray( GetPhone ) ).toArray();
		
		for( i = 0; i < GetPhoneArray.length; i++ )
					{
					 list = GetPhoneArray[ i ].Callee;
					}
			}


	if (result=="0")
	{
		alert("���� ��û ���� �Ǵ� ��û ��ȣ ����");
	}else{ 
		ReserveSMS += "�߽Ź�ȣ" + MsCaller +
			", ȸ�Ź�ȣ:" + sDisplay +
			", ���Ź�ȣ:" + list +
			", ����ð�:" + sSendDate +
			", �޽���:" + sMessage +
			", ���ڱ׷��ȣ:"+ result	+ "\n";
		}
	document.getElementById("SendReserveSMS").value =ReserveSMS;	
}
// ���๮�� ���� ��û �Լ�
function EditReserveSMS(){
		var	result		=	500;
		var MsCaller		=	document.all.MsCaller.value;
		var sSmsGroupSeqNo 	=	document.all.sSmsGroupSeqNo.value;
		var sDisplay		=	document.all.sDisplay.value;
		var sMessage		=	document.all.sMessage.value;
		var sSendDate		=	document.all.sSendDate.value;
		var ReserveSMS	 ="";
		var list		= "";

	GetPhone = KTOpenAPIX.GetRecvPhone();
			if( GetPhone != null && GetPhone != undefined )
					{
				var GetPhoneArray 	=	( new VBArray( GetPhone ) ).toArray();
				
				for( i = 0; i < GetPhoneArray.length; i++ )
							{
							 list = list + GetPhoneArray[ i ].Callee;
							}
					}

alert(list);

	result = KTOpenAPIX.EditReserveSMS( MsCaller, sSmsGroupSeqNo, sMessage, sSendDate);
	switch(result){
		case 0: alert("���� ��û ����"); break;
		case 200: alert("������ ��û ������"); 
			ReserveSMS += "�߽Ź�ȣ" + MsCaller +
				", ȸ�Ź�ȣ:" + sDisplay +
				", ���Ź�ȣ:" + list +
				", ����ð�:" + sSendDate +
				", �޽���:" + sMessage +
				", ���ڱ׷��ȣ:"+ sSmsGroupSeqNo; break;
		case 2000: alert("�α��� �Ǿ� ���� ����"); break;
		case 4200: alert("���� ���� �ð� ������ �߸���"); break;
		case 4201: alert("���� ���� �ð��� �߸���"); break;
		default : alert(result);
	}
		document.getElementById("SendReserveSMS").value =ReserveSMS;	

}
// ���� ���� ��� ��û �Լ�
function CancelReserveSMS(){
		var	result		=	500;
		var MsCaller		=	document.all.MsCaller.value;
		var sSendDate		=	document.all.sSendDate.value;
		
		result = KTOpenAPIX.CancelReserveSMS( MsCaller, sSendDate);
	switch(result){
		case 0: alert("���� ��û ����(�̹� �߼۵� ��� ����)"); break;
		case 200: alert("������ ��û ������"); break;
		case 2000: alert("�α��� �Ǿ� ���� ����"); break;
		default : alert(result);
	}
}

//������ ��ȭ ���� ����Ʈ ��û Ȯ�� �Լ�
function GetAbsenceCallList(){
		var CallList = "";
		var	KTDCallData	=	KTOpenAPIX.GetAbsenceCallListEx();
		
		if( KTDCallData != null && KTDCallData != undefined )
		{
			var KTDCallDataArray = ( new VBArray(KTDCallData)).toArray();

			for( i = 0; i < KTDCallDataArray.length; i++ )
			{
				var Kind = "";

				switch(KTDCallDataArray[ i ].Kind)
				{
					case 2 : Kind = "�߽�" ; break;
					case 3 : Kind = "����" ; break;
					case 4 : Kind = "������" ; break;
					case 5 : Kind = "����" ; break;
					case 7 : Kind = "�޴���" ; break;
					default : Kind = KTDCallDataArray[ i ].Kind; break;
				}
				
				CallList		+=	"�߽Ź�ȣ : "	 + KTDCallDataArray[ i ].Caller 	+ ", " + 
								", ���Ź�ȣ : "		 + KTDCallDataArray[ i ].Callee 	+ ", " +
								", ���ų�¥ : "		 + KTDCallDataArray[ i ].Date 	+ ", " +
								", �Ϸù�ȣ : "		 + KTDCallDataArray[ i ].DBID 	+ ", " +
								", ��� : "				+ KTDCallDataArray[ i ].Result + ", " +
								", ��ȭ���� ���� : "	 + Kind	+ "\n"; 
			}

		}else
		{
			CallList	=	"ã�� �� ����";
		}
		
		document.getElementById("GetAbsenceCallList").value =CallList;	
}
//��ȭ ���� �� ��û �Լ�
function GetCallCount(){
	var sKind		=	document.all.sKind.value;
	var kind	 =	"";
	switch (sKind)
	{
	case "1":	kind	 ="��ü ��ȭ���� �� : "; break;
	case "2":	kind	 ="�߽� ��ȭ���� �� : "; break;
	case "3":	kind	 ="���� ��ȭ���� �� : "; break;
	case "4":	kind	 ="������ ��ȭ���� �� : "; break;
	case "5":	kind	 ="���� : "; break;
	case "6":	kind	 ="�������� : "; break;
	case "7":	kind	 ="�ڵ��� : "; break;
	default : kind ="";
	
	}
		alert(kind + KTOpenAPIX.GetCallCount(sKind)  );
}
//��ȭ ���� ����Ʈ ��û Ȯ�� �Լ�
function GetCallList(){
	var sKind		=	document.all.sKind.value;
	var nStart		=  document.all.nStart.value;
	var nCount	=  document.all.nCount.value;
	var CallList = "";
		var	KTDCallData	=	KTOpenAPIX.GetCallListEx(sKind, nStart, nCount );
		
		if( KTDCallData != null && KTDCallData != undefined )
		{
			var KTDCallDataArray = ( new VBArray(KTDCallData)).toArray();

			for( i = 0; i < KTDCallDataArray.length; i++ )
			{

				var Kind = "";

				switch(KTDCallDataArray[ i ].Kind)
				{
					case 2 : Kind = "�߽�" ; break;
					case 3 : Kind = "����" ; break;
					case 4 : Kind = "������" ; break;
					case 5 : Kind = "����" ; break;
					case 7 : Kind = "�޴���" ; break;
					default : Kind = KTDCallDataArray[ i ].Kind; break;
				}
				
				CallList		+=	"�߽� : "	+ KTDCallDataArray[ i ].Caller	+
							", ���� : "		+ KTDCallDataArray[ i ].Callee	+
							", ���ų�¥ : " + KTDCallDataArray[ i ].Date	+
							", �Ϸù�ȣ : " + KTDCallDataArray[ i ].DBID	+
							", ��� : "		+ KTDCallDataArray[ i ].Result	+ 
							", ��ȭ���� ���� : "		+  Kind	+ 	"\n"; 
			}
		}
		else
		{
			CallList	=	"ã�� �� ����";
		}
	document.getElementById("GetAbsenceCallList").value =CallList;	
}
//��ȭ ���� �� ���� Ȯ�� �Լ�
function GetCall(){
	
	var CallsDBID = document.all.CallsDBID.value;
	var	KTDCallData  =	KTOpenAPIX.GetCallEx(CallsDBID);

if( KTDCallData != null && KTDCallData != undefined )
		{	
			var Kind = "";

				switch(KTDCallData.Kind)
				{
					case 2 : Kind = "�߽�" ; break;
					case 3 : Kind = "����" ; break;
					case 4 : Kind = "������" ; break;
					case 5 : Kind = "����" ; break;
					case 7 : Kind = "�޴���" ; break;
					default : Kind = KTDCallData.Kind; break;
				}

			alert(		"�߽� : "		+ KTDCallData.Caller	+	
				", ���� : "		+ KTDCallData.Callee	+
				", ���ų�¥ : " + KTDCallData.Date		+
				", �Ϸù�ȣ : " + KTDCallData.DBID		+
				", ��� : "		+ KTDCallData.Result  + 
				", ��ȭ���� ����  : "		+  Kind
						);	
		}else
		{
			alert("ã�� �� ����");
		}
}
//��ȭ ���� ���� ��û �Լ�
function DelCall()
{
	var	result		=	500;
	var CallsDBID = document.all.CallsDBID.value;
	result = KTOpenAPIX.DelCall(CallsDBID);
	if (result == "0")
	{
		alert("���� ��û ����(�̹� �߼۵� ��� ����)");
	}else if (result =="200")
	{	
		alert("������ ��û ������")
	}else if (result =="2000")
	{	
		alert("�α��� �Ǿ� ���� ����")
	}else {
		alert(result)	
	}

GetCallList();
}

//������ ���� ���� ����Ʈ ��û �Լ�
function GetAbsenceSmsList()
{
	var GetSmsList	= "";
	var	KTDCallData  =	KTOpenAPIX.GetAbsenceSmsList();

	if( KTDCallData != null && KTDCallData != undefined )
		{
	var KTDCallDataArray	=	( new VBArray( KTDCallData ) ).toArray();
	
		for( i = 0; i < KTDCallDataArray.length; i++ )
				{
					GetSmsList +=
						"�߽� : "			+ KTDCallDataArray[ i ].Caller	 +
						", ���� : "			+ KTDCallDataArray[ i ].Callee	 +
						", ���ų�¥ : "		+ KTDCallDataArray[ i ].Date	 +
						", �Ϸù�ȣ : "		+ KTDCallDataArray[ i ].DBID	 +
						", ���ڸ޽��� : "	+ KTDCallDataArray[ i ].Message  +
						", ��� : "			+ KTDCallDataArray[ i ].Result  + "\n";
										
				}	
				
		}else{
			GetSmsList = "ã�� �� ����"
		}
		document.getElementById("GetAbsenceSmsList").value =GetSmsList;	
}

//���� ���� �� ��û �Լ�
function GetSmsCount()
{
	var SmssKind = document.all.SmssKind.value;
	alert(KTOpenAPIX.GetSmsCount(SmssKind));
}

//���� ���� ����Ʈ ��û Ȯ�� �Լ�
function GetSmsList()
{	
	var GetSmsList	= ""; 
	var SmssKind	=	document.all.SmssKind.value;
	var SmsnStart	=	document.all.SmsnStart.value;
	var SmsnCount	=	document.all.SmsnCount.value;
	
		var	KTDCallData	=	KTOpenAPIX.GetSmsListEx(SmssKind, SmsnStart, SmsnCount );
		
		if( KTDCallData != null && KTDCallData != undefined )
		{
			var KTDCallDataArray = ( new VBArray(KTDCallData)).toArray();

			for( i = 0; i < KTDCallDataArray.length; i++ )
			{
				var Kind = "";

				switch(KTDCallDataArray[ i ].Kind)
				{
					case 2 : Kind = "�߽�" ; break;
					case 3 : Kind = "����" ; break;
					case 4 : Kind = "������" ; break;
					case 5 : Kind = "����" ; break;
					case 7 : Kind = "�޴���" ; break;
					default : Kind = KTDCallDataArray[ i ].Kind; break;
				}
				
				GetSmsList		+=	"�߽� : "		+	KTDCallDataArray[ i ].Caller	+
							", ���� : "			+	KTDCallDataArray[ i ].Callee	+
							", ���ų�¥ : "		+	KTDCallDataArray[ i ].Date		+
							", �Ϸù�ȣ : "		+	KTDCallDataArray[ i ].DBID		+
							", ���ڸ޼��� : "	+	KTDCallDataArray[ i ].Message	+
							", ��� : "			+	KTDCallDataArray[ i ].Result	+ 
							", ��ȭ���� ���� : "			+	Kind	+  "\n"; 
			}
		}
		else
		{
			GetSmsList	=	"ã�� �� ����";
		}

		document.getElementById("GetAbsenceSmsList").value =GetSmsList;	
}

//���� ���� �� ���� Ȯ�� �Լ�
function GetSms()
{
	var SmssDBID = document.all.SmssDBID.value;
	var	KTDCallData  =	KTOpenAPIX.GetSmsEx(SmssDBID);
		
		if( KTDCallData != null && KTDCallData != undefined )
		{
				var Kind = "";

				switch(KTDCallData.Kind)
				{
					case 2 : Kind = "�߽�" ; break;
					case 3 : Kind = "����" ; break;
					case 4 : Kind = "������" ; break;
					case 5 : Kind = "����" ; break;
					case 7 : Kind = "�޴���" ; break;
					default : Kind = KTDCallData.Kind; break;
				}

			alert(		"�߽� : "			+	KTDCallData.Caller	+
						", ���� : "			+	KTDCallData.Callee	+
						", ���ų�¥ : "		+	KTDCallData.Date	+
						", �Ϸù�ȣ : "		+	KTDCallData.DBID	+
						", ���ڸ޼��� : "	+	KTDCallData.Message +
						", ��� : "			+	KTDCallData.Result  + 
						", ��ȭ���� ���� : "			+	Kind
								);					
		}else{
			alert("ã�� �� ����");
		}
}

//���� ���� ���� �Լ�
function DelSms()
{
	var	result		=	500;
	var SmssDBID = document.all.SmssDBID.value;
	result = KTOpenAPIX.DelSms(SmssDBID);
	if (result == "0")
	{
		alert("���� ��û ����");
	}else if (result =="200")
	{	
		alert("������ ��û ������");
	}else if (result =="2000")
	{	
		alert("�α��� �Ǿ� ���� ����");
	}else {
		alert(result);
	}
	GetSmsList();
}
// ��ȭ �޸� �� ��û �Լ�
function GetCallMemoCount(){
		var cCmType = document.all.cCmType.value;
		var type ="";
		if (cCmType == "1")
		{
			type="���� ��ȭ �޸� ���� : ";
		}else if (cCmType == "2")
		{
			type="���� ��ȭ �޸� ���� : ";
		}
		alert(type + KTOpenAPIX.GetCallMemoCount(cCmType));
}

//��ȭ �޸� ���� ����Ʈ ��û �Լ�
function GetCallMemoList(){
		var cCmType = document.all.cCmType.value;

		var MemonStart = document.all.MemonStart.value;
		var MemonCount = document.all.MemonCount.value;
		var MemoList ="";
		var	KTDCallData	=	KTOpenAPIX.GetCallMemoList(cCmType, MemonStart, MemonCount );

	if( KTDCallData != null && KTDCallData != undefined )
		{
			var KTDCallDataArray = ( new VBArray(KTDCallData)).toArray();

			for( i = 0; i < KTDCallDataArray.length; i++ )
			{
				MemoList		+=	"�߽� : "		+	KTDCallDataArray[ i ].Caller	+
							", ���� : "			+	KTDCallDataArray[ i ].Callee	+
							", ��ȭ�����Ϸù�ȣ : "		+	KTDCallDataArray[ i ].CLDBID 		+
							", ��ȭ�޸��Ϸù�ȣ : "		+	KTDCallDataArray[ i ].DBID 		+
							", �߽����̸� : "		+	KTDCallDataArray[ i ].CallName 	+
							", ��ȭ�޸� : "	+	KTDCallDataArray[ i ].Memo 	+
							", �޸�׷� : "	+	KTDCallDataArray[ i ].MemoGroup 	+
							", ������� : "	+	KTDCallDataArray[ i ].ProgGroup 	+
							", ���ų�¥ : "	+	KTDCallDataArray[ i ].Date 	+
							", �޸�Ÿ�� : "	+	KTDCallDataArray[ i ].Type 	+
							", ���������ð� : "			+	KTDCallDataArray[ i ].UDate 	+ "\n"; 
			}
		}
		else
		{
			MemoList	=	"ã�� �� ����";
		}

		document.getElementById("GetCallMemoCount").value =MemoList;	
}

//��ȭ �޸� ���� ��û �Լ� 
function GetCallMemo(){
		var cCmType = document.all.cCmType.value;
		var MemosDBID  = document.all.MemosDBID  .value;
		var KTDCallData = KTOpenAPIX.GetCallMemo(cCmType, MemosDBID);
		var type="";
		if( KTDCallData != null && KTDCallData != undefined )
		{
		
		if (KTDCallData.Type == "1")
		{
			type = "���� ��ȭ �޸�";
		}else if (KTDCallData.Type == "2")
		{
			type = "���� ��ȭ �޸�";
		}
		alert(		"�߽� : "			+	KTDCallData.Caller	+
				", ���� : "			+	KTDCallData.Callee	+
				", ��ȭ�����Ϸù�ȣ : "		+	KTDCallData.CLDBID 	+
				", ��ȭ�޸��Ϸù�ȣ : "		+	KTDCallData.DBID	+
				", �߽��� : "		+	KTDCallData.CallName  +
				", ��ȭ�޸� : "	+	KTDCallData.Memo  +
				", �޸�׷� : "	+	KTDCallData.MemoGroup  +
				", ������� : "	+	KTDCallData.ProgGroup  +
				", ���ų�¥ : "	+	KTDCallData.Date  +
				", ��ȭ���� ���� �Ϸù�ȣ : "	+	KTDCallData.LogicID  +
				", Ÿ�� : "	+	type  +
				", ���������ð� : "			+	KTDCallData.UDate  
					);					
		}else{
			alert("ã�� �� ����");
		}

}

//��ȭ �޸� �߰� ��û �Լ�
function NewCallMemo(){
		var	result		=	500;
		var cCmType = document.all.cCmType.value;
		var sCLDBID = document.all.sCLDBID.value;
		var sCallName = document.all.sCallName.value;
		var sCallGroup = document.all.sCallGroup.value;
		var sProgGroup = document.all.sProgGroup.value;
		var sMemo = document.all.sMemo.value;
		
		result = KTOpenAPIX.NewCallMemo(cCmType, sCLDBID, sCallName, sCallGroup,sProgGroup, sMemo);
		
		if (result == "0")
		{
			alert("�α��� �Ǿ� ���� �ʰų� ��û ����");
		}else{		
			alert("�ű� ��ȭ �޸� �Ϸù�ȣ :" + result)
		}
 	GetCallMemoList();
}
//��ȭ �޸� ���� ��û �Լ�
function EditCallMemo(){
		
		var	result		=	500;
		var cCmType = document.all.cCmType.value;
		var MemosDBID = document.all.MemosDBID.value;
		var sCallName = document.all.sCallName.value;
		var sCallGroup = document.all.sCallGroup.value;
		var sProgGroup = document.all.sProgGroup.value;
		var sMemo = document.all.sMemo.value;

		result = KTOpenAPIX.EditCallMemo(cCmType, MemosDBID, sCallName, sCallGroup,sProgGroup, sMemo);

	switch (result)
	{
	case 200: alert("��ȭ�޸� ���� ��û ����");break;
	case 2000: alert("�α��� �Ǿ� ���� ����");break;
	case 4300: alert("��ȭ �޸� �̸� ���̰� 30byte�� �ʰ�"); break;
	case 4301: alert("��ȭ �޸� ���̰� 256byte�� �ʰ�"); break;
	default : alert("�ű� ��ȭ �޸� �Ϸù�ȣ" + result );	
	}
	GetCallMemoList();
}
//��� �޸� ���� ��û �Լ�
function DelCallMemo(){
		var	result		=	500;
		var cCmType = document.all.cCmType.value;
		var MemosDBID = document.all.MemosDBID.value;
		result	=	KTOpenAPIX.DelCallMemo(cCmType, MemosDBID);
		switch (result)
		{
		case 200: alert("��ȭ�޸� ���� ��û ����");break;
		case 2000: alert("�α��� �Ǿ� ���� ����");break;
		case 4300: alert("��ȭ �޸� �̸� ���̰� 30byte�� �ʰ�"); break;
		case 4301: alert("��ȭ �޸� ���̰� 256byte�� �ʰ�"); break;
		default : alert("�ű� ��ȭ �޸� �Ϸù�ȣ" + result );	
		}
		GetCallMemoList();
}

//�ּҷ� �׷� ����Ʈ ��û �Լ�
function GetAddressGroupList(){
		var cAgType = document.all.cAgType.value;
		var sPDBID = document.all.sPDBID.value;
		var GroupList ="";
		var	KTDCallData	=	KTOpenAPIX.GetAddressGroupList(cAgType, sPDBID);

	if( KTDCallData != null && KTDCallData != undefined )
		{
			var KTDCallDataArray = ( new VBArray(KTDCallData)).toArray();

			for( i = 0; i < KTDCallDataArray.length; i++ )
			{
				GroupList		+=	"�׷� �Ϸù�ȣ : "		+	KTDCallDataArray[ i ].DBID 	+
							", �׷� Ÿ�� : "			+	KTDCallDataArray[ i ].Type 	+
							", �׷� �̸� : "		+	KTDCallDataArray[ i ].Name  		+
							", �θ� �׷� �Ϸù�ȣ : "		+	KTDCallDataArray[ i ].PDBID  		+
							 "\n"; 
			}
		}
		else
		{
			GroupList	=	"ã�� �� ����";
		}

		document.getElementById("GetAddressGroupList").value =GroupList;	
}

//�ּҷ� �׷� ���� �������� ��û �Լ�
function GetAddressGroup(){
		var cAgType = document.all.cAgType.value;
		var GroupsDBID = document.all.GroupsDBID.value;
		var	KTDCallData	=	KTOpenAPIX.GetAddressGroup(cAgType, GroupsDBID);
	
	if( KTDCallData != null && KTDCallData != undefined )
		{
		alert (	
			"�׷� �Ϸ� ��ȣ : " + KTDCallData.DBID	+	
			", �׷� Ÿ��" + KTDCallData.Type 	+
			", �׷� �̸�" + KTDCallData.Name 	+
			", �θ� �׷� �Ϸù�ȣ" + KTDCallData.PDBID 
		);		
		}else {
			alert ("ã�� �� ����")
		}
}

//�ű� �ּҷ� �׷� �߰� �Լ�
function NewAddressGroup(){
		var result = 500;
		var cAgType = document.all.cAgType.value;
		var sAgName = document.all.sAgName.value;
		var sPDBID = document.all.sPDBID.value;
		
		result	=	KTOpenAPIX.NewAddressGroup(cAgType, sAgName, sPDBID);

		if (result == "0")
		{
			alert("�ű� �ּҷ� �׷� �߰� ��û ����");
		}else{
			alert("�ű� �ּҷ� �׷� �Ϸù�ȣ : " + result);
		}
		GetAddressGroupList();
}

//�ּҷ� �׷� ���� �Լ�
function EditAddressGroup(){
		var result = 500;
		var cAgType = document.all.cAgType.value;
		var sPDBID = document.all.sPDBID.value;
		var sAgName = document.all.sAgName.value;
		var GroupsDBID = document.all.GroupsDBID.value;

		result	=	KTOpenAPIX.EditAddressGroup(cAgType, sAgName, sPDBID, GroupsDBID);
		switch (result)
		{
		case 0: alert("�ּҷ� ���� ��û ����"); break;
		case 200: alert("�ּҷ� �׷� ���� ��û ����"); break;
		case 2000: alert("�α��� �Ǿ� ���� ����"); break;
		case 4400: alert("������ �ּҷ� �׷� �̸��� ����"); break;
		case 4401: alert("�ּҷ� �׷��� ã�� �� ����"); break;
		case 4402: alert("�̹� ������ �ּҷ� �׷��� ������ ����"); break;
		case 4301: alert("��ȭ �޸� ���̰� 256 byte�� �ʰ�"); break;
		default: alert(result);	
		}
		GetAddressGroupList();
}

// �ּҷ� �׷� ���� �Լ�
function DelAddressGroup(){
		var result = 500;
		var cAgType = document.all.cAgType.value;
		var GroupsDBID = document.all.GroupsDBID.value;
		result =  KTOpenAPIX.DelAddressGroup(cAgType, GroupsDBID);
		switch (result)
		{
		case 0: alert("�ּҷ� �׷� ���� ��û ����"); break;
		case 200: alert("�ּҷ� �׷� ���� ��û ����"); break;
		case 2000: alert("�α��� �Ǿ� ���� ����"); break;
		case 4401: alert("�ּҷ� �׷��� ã�� �� ����"); break;
		default: alert(result);	
		}
		GetAddressGroupList();
}

//�ּҷ� ����� ����Ʈ ��û �Լ�
function GetAddressDataList(){
		var DatacAgType = document.all.DatacAgType.value;
		var DatasPDBID  = document.all.DatasPDBID.value;
		
		var DataList ="";
		var	KTDCallData	=	KTOpenAPIX.GetAddressDataList(DatacAgType, DatasPDBID);

	if( KTDCallData != null && KTDCallData != undefined )
		{
			var KTDCallDataArray = ( new VBArray(KTDCallData)).toArray();

			for( i = 0; i < KTDCallDataArray.length; i++ )
			{
				DataList		+=	"�Ϸù�ȣ : "		+	KTDCallDataArray[ i ].DBID 	+
							", �̸� : "			+	KTDCallDataArray[ i ].Name         	+
							", Ÿ�� : "		+	KTDCallDataArray[ i ].Type           		+
							", ���ּ� : "		+	KTDCallDataArray[ i ].Address       		+
							", ������� : "		+	KTDCallDataArray[ i ].BirthDay      		+
							", ����0, ���1 : "		+	KTDCallDataArray[ i ].BirthDay      		+
							", ���� : "		+	KTDCallDataArray[ i ].Business      		+
							", ȸ��� : "		+	KTDCallDataArray[ i ].CompanyName   		+
							", �μ��� : "		+	KTDCallDataArray[ i ].Department    		+
							", �̸��� : "		+	KTDCallDataArray[ i ].Email         		+
							", ���ɻ� : "		+	KTDCallDataArray[ i ].Favorite      		+
							", �ѽ� : "		+	KTDCallDataArray[ i ].FaxNum        		+
							", �� ��ȭ : "		+	KTDCallDataArray[ i ].HomeNum       		+
							", ��õ�� : "		+	KTDCallDataArray[ i ].Keyman        		+
							", �ι��޸� : "		+	KTDCallDataArray[ i ].Memo          		+
							", ������Ȳ : "		+	KTDCallDataArray[ i ].MetChance     		+
							", �ڵ�����ȣ : "		+	KTDCallDataArray[ i ].MobileNum     		+
							", ȸ����ȭ : "		+	KTDCallDataArray[ i ].OfficeNum     		+
							", ��å : "		+	KTDCallDataArray[ i ].Position      		+
							", �����ȣ : "		+	KTDCallDataArray[ i ].ZipCode       		+
							"\n"; 
			}
		}
		else
		{
			DataList =	 "ã�� �� ����"
		}

		document.getElementById("GetAddressDataList").value =DataList;	
		
}

//�ּҷ� ����� ���� ��û �Լ�
function GetAddressData(){
			var DatacAgType = document.all.DatacAgType.value;
			var DatasDBID  = document.all.DatasDBID.value;

			var	KTDAddressData   =	KTOpenAPIX.GetAddressData(DatacAgType, DatasDBID);
						
						alert(
						"�Ϸù�ȣ: "	+	KTDAddressData.DBID	+
						", �̸�: "			+	KTDAddressData.Name	+
						", Ÿ��: "	+	KTDAddressData. Type	+
						", ���ּ�: "		+	KTDAddressData.Address	+
						", �������: "		+	KTDAddressData.BirthDay	+
						", ����0,���1: "	+	KTDAddressData.BirthType	+
						", ����: "			+	KTDAddressData.Business	+
						", ȸ���: "		+	KTDAddressData.CompanyName	 	+
						", �μ���: "		+	KTDAddressData.Department	+
						", �̸����ּ�: "	+	KTDAddressData.Email	+
						", ���ɻ�: "		+	KTDAddressData.Favorite	+
						", �ѽ���ȣ: "		+	KTDAddressData.FaxNum	+
						", ����ȭ��ȣ: "	+	KTDAddressData.HomeNum	+
						", ��õ��: "		+	KTDAddressData.Keyman	+
						", �ι��޸�: "		+	KTDAddressData.Memo	+
						", ������Ȳ: "		+	KTDAddressData.MetChance	+
						", �ڵ���: "	+	KTDAddressData.MobileNum	+
						", ȸ����ȭ: "	+	KTDAddressData.OfficeNum	+
						", ��å: "			+	KTDAddressData.Position	+
						", �����ȣ: "		+	KTDAddressData.ZipCode
						);			
				document.getElementById("sAdName").value = KTDAddressData.Name;	
				document.getElementById("sAdAddress").value = KTDAddressData.Address;
				document.getElementById("sAdBirthDay").value = KTDAddressData.BirthDay;
				document.getElementById("cAdBirthType").value = KTDAddressData.BirthType;
				document.getElementById("sAdBusiness").value = KTDAddressData.Business;
				document.getElementById("sAdCompany").value = KTDAddressData.CompanyName;
				document.getElementById("sAdTeam").value = KTDAddressData.Department;
				document.getElementById("sAdEmail").value = KTDAddressData.Email;
				document.getElementById("sAdFNumber").value = KTDAddressData.FaxNum;
				document.getElementById("sAdFavorite").value = KTDAddressData.Favorite;
				document.getElementById("sAdHNumber").value = KTDAddressData.HomeNum;
				document.getElementById("sAdKeyMan").value = KTDAddressData.Keyman;
				document.getElementById("sAdMemo").value = KTDAddressData.Memo;
				document.getElementById("sAdMetChange").value = KTDAddressData.MetChance;
				document.getElementById("sAdMNumber").value = KTDAddressData.MobileNum;
				document.getElementById("sAdONumber").value = KTDAddressData.OfficeNum;
				document.getElementById("sAdTitle").value = KTDAddressData.Position;
				document.getElementById("sAdZipCode").value = KTDAddressData.ZipCode;
				

}

//�ּҷ� ����� �߰� ��û �Լ�
function NewAddressData(){
			var result = 500;
			var DatacAgType = document.all.DatacAgType.value;
			var DatasPDBID  = document.all.DatasPDBID.value;
			var sAdName  = document.all.sAdName.value;
			var sAdMNumber  = document.all.sAdMNumber.value;
			var sAdONumber  = document.all.sAdONumber.value;
			var sAdHNumber  = document.all.sAdHNumber.value;
			var sAdFNumber  = document.all.sAdFNumber.value;
			var sAdCompany  = document.all.sAdCompany.value;
			var sAdTeam  = document.all.sAdTeam.value;
			var sAdTitle  = document.all.sAdTitle.value;
			var sAdBusiness  = document.all.sAdBusiness.value;
			var sAdZipCode  = document.all.sAdZipCode.value;
			var sAdAddress  = document.all.sAdAddress.value;
			var sAdEmail  = document.all.sAdEmail.value;
			var sAdMemo  = document.all.sAdMemo.value;
			var sAdBirthDay  = document.all.sAdBirthDay.value;
			var cAdBirthType  = document.all.cAdBirthType.value;
			var sNgName  = document.all.sNgName.value;
			var sAdMetChance  = document.all.sAdMetChance.value;
			var sAdKeyman  = document.all.sAdKeyman.value;
			var sAdFavorite  = document.all.sAdFavorite.value;

	result   =	KTOpenAPIX.NewAddressData( DatacAgType, DatasPDBID, sAdName,  sAdMNumber, sAdONumber,  sAdHNumber, sAdFNumber,  sAdCompany, sAdTeam,  sAdTitle, sAdBusiness,  sAdZipCode, sAdAddress,  sAdEmail, sAdMemo,  sAdBirthDay, cAdBirthType,  sNgName, sAdMetChance,  sAdKeyman, sAdFavorite);

			if (result =="0")
			{
				alert("�ּҷ� ����� �߰� ���� ��û ����");
			}else{
				alert("�Ϸù�ȣ" + result);
			}
			GetAddressDataList()
}

//�ּҷ� ����� ���� �Լ�
function EditAddressData(){
		var result = 500;
		var DatacAgType = document.all.DatacAgType.value;
			var DatasPDBID  = document.all.DatasPDBID.value;
			var DatasDBID  = document.all.DatasDBID.value;
			var sAdName  = document.all.sAdName.value;
			var sAdMNumber  = document.all.sAdMNumber.value;
			var sAdONumber  = document.all.sAdONumber.value;
			var sAdHNumber  = document.all.sAdHNumber.value;
			var sAdFNumber  = document.all.sAdFNumber.value;
			var sAdCompany  = document.all.sAdCompany.value;
			var sAdTeam  = document.all.sAdTeam.value;
			var sAdTitle  = document.all.sAdTitle.value;
			var sAdBusiness  = document.all.sAdBusiness.value;
			var sAdZipCode  = document.all.sAdZipCode.value;
			var sAdAddress  = document.all.sAdAddress.value;
			var sAdEmail  = document.all.sAdEmail.value;
			var sAdMemo  = document.all.sAdMemo.value;
			var sAdBirthDay  = document.all.sAdBirthDay.value;
			var cAdBirthType  = document.all.cAdBirthType.value;
			var sNgName  = document.all.sNgName.value;
			var sAdMetChance  = document.all.sAdMetChance.value;
			var sAdKeyman  = document.all.sAdKeyman.value;
			var sAdFavorite  = document.all.sAdFavorite.value;

	result   =	KTOpenAPIX.EditAddressData( DatacAgType, DatasPDBID, DatasDBID, sAdName,  sAdMNumber, sAdONumber,  sAdHNumber, sAdFNumber,  sAdCompany, sAdTeam,  sAdTitle, sAdBusiness,  sAdZipCode, sAdAddress,  sAdEmail, sAdMemo,  sAdBirthDay, cAdBirthType,  sNgName, sAdMetChance,  sAdKeyman, sAdFavorite);
	
	switch (result)
	{
	case 0: alert("�ּҷ� ����� ���� ���� ��û ����"); break;
	case 200: alert("�ּҷ� �׷� ���� ��û ����"); break;
	case 2000: alert("�α��� �Ǿ� ���� ����"); break;
	case 4401: alert("�ּҷ� �׷��� ã�� �� ����"); break;
	case 4500: alert("�ּҷ� ����� �̸��� ����"); break;
	case 4502: alert("�ּҷ� ����� ��ȭ��ȣ�� �ϳ��� �������� ����"); break;
	default:alert(result);
	}
	GetAddressDataList()
}
//�ּҷ� ����� ���� �Լ�
function DelAddressData(){
			var result	=	500;
			var DatacAgType = document.all.DatacAgType.value;
			var DatasPDBID  = document.all.DatasPDBID.value;
			var DatasDBID  = document.all.DatasDBID.value;
		result   =	KTOpenAPIX.DelAddressData( DatacAgType,DatasPDBID, DatasDBID);
	
	switch (result)
	{
	case 0: alert("�ּҷ� ����� ���� ���� ��û ����"); break;
	case 200: alert("�ּҷ� �׷� ���� ��û ����"); break;
	case 2000: alert("�α��� �Ǿ� ���� ����"); break;
	case 4500: alert("�ּҷ� ����� �̸��� ����"); break;
	case 4501: alert("�ּҷ� ����ڸ� ã�� �� ����"); break;
	default:alert(result);
	}
	GetAddressDataList()
}