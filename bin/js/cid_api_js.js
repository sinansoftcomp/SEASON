//텍스트 박스 초기화 
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

//OPEN_API 버전 확인 함수
function getver(){
	document.getElementById("verBox").innerText = KTOpenAPIX.GetApiVer();
}


//로그인 요청 함수
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
		case 301:alert("CID 다른 위치에서 로그인");break;
		case 401:alert("CID 미등록 아이디로 로그인"); break;
		case 402:alert("CID 비밀번호 오류 횟수 초과 (5회제한)"); break;
		case 403: alert("CID 임시비밀호 로그인");break;
		case 404:alert("CID 임시비밀번호 설정"); break;
		case 405:alert("CID 비밀번호 오류"); break;
		case 407:alert("CID 접속 IP 오류"); break;
		case 408: alert("CID 미등록 PC");break;
		case 500: alert("CID 기타(HTTPS/HTTP 요청 실패)");break;
		case 1000:alert("CID 이미 로그인중"); break;
		case 1001:alert("CID 서버 타입 에러"); break;
		case 1502:alert("CID 협정 만료일이 지났음"); break;
		case 1503: alert("CID 인증키 유효기간이 지났음");break;
		case 1504:alert("CID 인증키 비활성"); break;
		case 1505: alert("CID 인증키 타입이 틀릴 경우");break;
		case 1506: alert("CID 개발 서버이나 상용 인증키, 상용 Flag일 경우");break;
		case 1507: alert("CID 상용 서버이나 개발 인증키, 개발 Flag일 경우");break;
		case 1700: alert("CID API 환경 정보 얻지 못함(실행되는 경로)");break;
		case 1701: alert("CID KTA_API.dat / KTD_API.dat등의 data 파일 초기화 에러 파일이 존재해야 함"); break;	 
		case 1702: alert("CID PC 메모리 부족(API 생성 에러)");break;

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

//로그아웃 요청 함수

function  Logout(){
  var result = 500;
	result = confirm("로그아웃 하시겠습니까?");
	 if (result == true)
	 {
		 KTOpenAPIX.Logout();
	 } 
}

//기존 사용자 강제 로그아웃 요청 함수
function LoginKickOut(){
	var re = confirm("강제로그아웃 하시겠습니까?");
	var result = 500;
	if (re == true)
	{
		 result = KTOpenAPIX.LoginKickOut();
		 if (result == "0")
		{
			alert("중복로그인 상태가 아님");
		}else {
			alert(result)
		}
	}
	
}

//회선청약 요청 함수
function  LineJoin(){
	var Authkey		=	document.all.authkey.value;
	KTOpenAPIX.LineJoin(Authkey);
}
// 도움말 확장 함수
function HelpEX(){
	var Server			=	document.all.server.value;
	KTOpenAPIX.HelpEX(Server);
}

//회원 가입 확장 합수
function UserJoinEX(){
var Server			=	document.all.server.value;
	KTOpenAPIX.UserJoinEx(Server)
}
// 패스워드 찾기 확장 함수
function FindPasswdEX(){
	var Server			=	document.all.server.value;
	KTOpenAPIX.FindPasswdEx(Server)
}
// 설정 변경 함수
function SetMyInfo(){
	KTOpenAPIX.SetMyInfo()
}

//가입 전화번호 리스트 정보 및 설정 값 구하기 함수

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
							LineState = "정상통화"; 
						}else if(KTDPhoneArray[ i ].LineState == "1"){
							LineState = "착신전환";
						}else if(KTDPhoneArray[ i ].LineState == "2"){
							LineState = "부재중 착신전환";
						}else if(KTDPhoneArray[ i ].LineState == "3"){
							LineState = "부재중 멘트";
						}
					
					GetPhoneList += "회선번호 : "			+  KTDPhoneArray[ i ].Telnum		+
						", 전화 발신 : "	+	Ctc			+  
						", 국제전화 : "		+	Intercall		+
						", 시회전화 : "		+	Tollline		+
						", 시내전화 : "		+	Localcall	+
						", 휴대전화 : "		+	Mobile		+
						", 발신정보 표시 : "		+ Cid		+
						", SMS발신 : "	+	Smssend	+
						", SMS수신 : "	+	Smsrecv	+
						", 대표번호 : "		+	Mainnum	+
						", 회선상태 : "	 	+	LineState	+
						", 착신전환 : "		+	KTDPhoneArray[ i ].RecvTel		+  "\n"; 
						
				}				
					
		}
		document.getElementById("GetPhoneList").value = GetPhoneList;
}


//가입 무선 전화번호 리스트 정보 및 설정 값 구하기 함수

function GetMobilePhoneList()
{

	var MobilePhoneInfo = "";

	var KTDMPhoneList = KTOpenAPIX.GetMobilePhoneList()


	

	if( KTDMPhoneList != null && KTDMPhoneList != undefined )
	{
		var KTDPhoneArray	=	( new VBArray( KTDMPhoneList ) ).toArray();

		for( i = 0; i < KTDPhoneArray.length; i++ )
		{

			MobilePhoneInfo += "무선 전화번호 : "  + KTDPhoneArray[i].MobilePhoneNum  +
							", CID서비스 가입여부 : " +  KTDPhoneArray[i].MobileCID  +
							", 정상서비스 여부 : " +  KTDPhoneArray[i].MobileStatus + "\n";
		}
	}
	else
	{
		MobilePhoneInfo = "가입된 무선 전화번호가 없음	";
	}
	document.getElementById("GetMobilePhoneList").value = MobilePhoneInfo;
}


//회선의 통화 상태(정상통화/착신전환/부재중 착신전환) 설정 함수
function SetLineState(){
	var result = 500;
	var sTelNum = document.all.sTelNum.value;
	var sTelState = document.all.sTelState.value;
	var sRecvTel = document.all.sRecvTel.value;
	
	result = KTOpenAPIX.SetLineState( sTelNum, sTelState, sRecvTel);

	switch (result)
	{
	case 0: alert("기타 오류"); break;
	case 200: alert("서버로 부터 요청 성공함"); break;
	case 4002: alert("사용가능 회선이 아님"); break;
	case 4110: alert("통화 상태 변경 권한 없음"); break;
	case 4111: alert("통화 상태 번호가 잘못됨(sTelState 확인)"); break;
	case 1600: alert("네트웍 요청 에러"); break;
	case 1601: alert("네트웍 요청 에러"); break;
	default:alert(result);
	}
	
}


//전화번호 셋팅 함수

function SetRecvPhone(){
	var	result		=	500;
	var sRecvPhone = document.all.sRecvPhone.value;
		result = KTOpenAPIX.SetRecvPhone(sRecvPhone)
		
		switch (result)
		{
		case 0: alert("기타오류"); break;
		case 200: alert("서버로 요청 성공함"); break;
		case 2000: alert("로그인 되어 있지 않음"); break;
		case 3001: alert("수신번호 오류(자릿수 최소 8 자리)"); break;
		case 4005: alert("수신번호 오류"); break;
		case 4004: alert("수신번호 개수 오류(최대32개)"); break;
		default : alert(result);
		}
		GetRecvPhone();	
}

//세팅된 전화번호 가져오는 함수
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
					RecvPhonelist		+=	"찾을 수 없음";
					}
		document.getElementById("RecvPhonelist").value =RecvPhonelist;	
}

//세팅된 전화번호 삭제 함수
function RemoveRecvPhone(){
	var	result		=	500;
		var sRecvPhone = document.all.sRecvPhone.value;
		result	=	KTOpenAPIX.RemoveRecvPhone(sRecvPhone);
		switch (result)
		{
		case 0: alert("기타오류"); break;
		case 200: alert("서버로 요청 성공함");break;
		case 2000: alert("로그인 되어 있지 않음"); break;
		case 4001: alert("전화번호 존재하지 않음"); break;
		default: alert(result);
		}
		GetRecvPhone();	
}

//세팅된 전화번호 전체삭제 함수
function RemoveAllRecvPhone(){
	var	result		=	500;
		result	=	KTOpenAPIX.RemoveAllRecvPhone();
		switch (result)
		{
		case 0: alert("기타 오류"); break;
		case 200: alert("서버로 요청 성공함"); break;
		case 2000: alert("로그인 되어 있지 않음"); break;
		default : alert(result);
		}
		GetRecvPhone();	
}

//전화걸기 요청 함수
function SendCTC(){
	var	result		=	500;
		var sCaller		=	document.all.sCaller.value;
		var sCallee	=	document.all.sCallee.value;
		result	=	KTOpenAPIX.SendCTC(sCaller, sCallee);
		
		switch (result)
		{
		case 200: alert("서버로 요청성공함"); break;
		case 2000: alert("로그인 되어있지 않음"); break;
		case 3000: alert("수신/발신 번호가 동일"); break;
		case 3001: alert("수신번호 오류(자리수 최소 8자리)"); break;
		case 4002: alert("전화걸기를 할 수 있는 전화번호가 아님"); break;
		case 4101: alert("핸드폰에 전화를 걸 권한이 없음"); break;
		case 4102: alert("국제전화를 걸 권한이 없음"); break;
		case 4103: alert("시내전화를 걸 권한이 없음"); break;
		case 4104: alert("시외전화를 걸 권한이 없음"); break;
		default : alert(result);
		}

}
//다자통화 요청 함수
function SendCON(){
	var result = 500;
	var sCaller	 =	document.all.sCaller.value;
	var sRecvPhone	 =	document.all.sRecvPhone.value;
		KTOpenAPIX.SetRecvPhone( sRecvPhone );
		result = KTOpenAPIX.SendCON(sCaller);
	alert(result);
	switch (result)
		{
		case 200: alert("서버로 요청성공함"); break;
		case 2000: alert("로그인 되어있지 않음"); break;
		case 3000: alert("수신/발신 번호가 동일"); break;
		case 3001: alert("수신번호 오류(자리수 최소 8자리)"); break;
		case 4002: alert("전화걸기를 할 수 있는 전화번호가 아님"); break;
		case 4101: alert("핸드폰에 전화를 걸 권한이 없음"); break;
		case 4102: alert("국제전화를 걸 권한이 없음"); break;
		case 4103: alert("시내전화를 걸 권한이 없음"); break;
		case 4104: alert("시외전화를 걸 권한이 없음"); break;
		default : alert(result);
		}
}

//다자통화 사용자 추가 함수
function SendCONAddUser()
	{
		var	result		=	500;
		var sCaller		=	document.all.sCaller.value;
		var sCallee	=	document.all.sCallee.value;
		var sDBID		=	document.all.sDBID.value;
		result = KTOpenAPIX.SendCONAddUser(sDBID, sCaller, sCallee ); 
		switch (result)
		{
		case 200: alert("서버로 요청성공함"); break;
		case 2000: alert("로그인 되어있지 않음"); break;
		case 3000: alert("수신/발신 번호가 동일"); break;
		case 3001: alert("수신번호 오류(자리수 최소 8자리)"); break;
		case 4002: alert("전화걸기를 할 수 있는 전화번호가 아님"); break;
		case 4101: alert("핸드폰에 전화를 걸 권한이 없음"); break;
		case 4102: alert("국제전화를 걸 권한이 없음"); break;
		case 4103: alert("시내전화를 걸 권한이 없음"); break;
		case 4104: alert("시외전화를 걸 권한이 없음"); break;
		default : alert(result);
		}

	}
//다자통화 사용자 삭제 함수
function SendCONKickOutUser(){
		var	result		=	500;
		var sCaller		=	document.all.sCaller.value;
		var sCallee	=	document.all.sCallee.value;
		var sDBID		=	document.all.sDBID.value;
		result = KTOpenAPIX.SendCONKickOutUser(sDBID, sCaller, sCallee ); 
		switch (result)
		{
		case 200: alert("서버로 요청성공함"); break;
		case 2000: alert("로그인 되어있지 않음"); break;
		case 3000: alert("수신/발신 번호가 동일"); break;
		case 3001: alert("수신번호 오류(자리수 최소 8자리)"); break;
		case 4002: alert("전화걸기를 할 수 있는 전화번호가 아님"); break;
		case 4101: alert("핸드폰에 전화를 걸 권한이 없음"); break;
		case 4102: alert("국제전화를 걸 권한이 없음"); break;
		case 4103: alert("시내전화를 걸 권한이 없음"); break;
		case 4104: alert("시외전화를 걸 권한이 없음"); break;
		default : alert(result);
		}
}
//문자 전송요청 함수
function SendSMS(){
		var	result		=	500;
		var MsCaller		=	document.all.MsCaller.value;
		var sDisplay	=	document.all.sDisplay.value;
		var sMessage		=	document.all.sMessage.value;

	result = KTOpenAPIX.SendSMS( MsCaller, sDisplay, sMessage);
		switch(result){
		case 0: alert("서버 요청 실패"); break;
		case 200: alert("서버로 요청 성공함"); break;
		case 2000: alert("로그인 되어 있지 않음"); break;
		case 4001: alert("수신번호 없음"); break;
		case 4003: alert("메세지 없음"); break;
		case 4004: alert("SMS 발신 한도 초과"); break;
		case 4005: alert("발신번호 오류"); break;
		case 4006: alert("메세지 길이 오류(최대 80bytes)"); break;
		default : alert(result);
	}
}

// 예약 문자 전송 함수 
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
		alert("서버 요청 실패 또는 요청 번호 에러");
	}else{ 
		ReserveSMS += "발신번호" + MsCaller +
			", 회신번호:" + sDisplay +
			", 수신번호:" + list +
			", 예약시간:" + sSendDate +
			", 메시지:" + sMessage +
			", 문자그룹번호:"+ result	+ "\n";
		}
	document.getElementById("SendReserveSMS").value =ReserveSMS;	
}
// 예약문자 수정 요청 함수
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
		case 0: alert("서버 요청 실패"); break;
		case 200: alert("서버로 요청 성공함"); 
			ReserveSMS += "발신번호" + MsCaller +
				", 회신번호:" + sDisplay +
				", 수신번호:" + list +
				", 예약시간:" + sSendDate +
				", 메시지:" + sMessage +
				", 문자그룹번호:"+ sSmsGroupSeqNo; break;
		case 2000: alert("로그인 되어 있지 않음"); break;
		case 4200: alert("문자 보낼 시간 포맷이 잘못됨"); break;
		case 4201: alert("문자 보낼 시간이 잘못됨"); break;
		default : alert(result);
	}
		document.getElementById("SendReserveSMS").value =ReserveSMS;	

}
// 예약 문자 취소 요청 함수
function CancelReserveSMS(){
		var	result		=	500;
		var MsCaller		=	document.all.MsCaller.value;
		var sSendDate		=	document.all.sSendDate.value;
		
		result = KTOpenAPIX.CancelReserveSMS( MsCaller, sSendDate);
	switch(result){
		case 0: alert("서버 요청 실패(이미 발송된 경우 포함)"); break;
		case 200: alert("서버로 요청 성공함"); break;
		case 2000: alert("로그인 되어 있지 않음"); break;
		default : alert(result);
	}
}

//부재중 통화 내역 리스트 요청 확장 함수
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
					case 2 : Kind = "발신" ; break;
					case 3 : Kind = "수신" ; break;
					case 4 : Kind = "부재중" ; break;
					case 5 : Kind = "실패" ; break;
					case 7 : Kind = "휴대폰" ; break;
					default : Kind = KTDCallDataArray[ i ].Kind; break;
				}
				
				CallList		+=	"발신번호 : "	 + KTDCallDataArray[ i ].Caller 	+ ", " + 
								", 수신번호 : "		 + KTDCallDataArray[ i ].Callee 	+ ", " +
								", 수신날짜 : "		 + KTDCallDataArray[ i ].Date 	+ ", " +
								", 일련번호 : "		 + KTDCallDataArray[ i ].DBID 	+ ", " +
								", 결과 : "				+ KTDCallDataArray[ i ].Result + ", " +
								", 통화내역 종류 : "	 + Kind	+ "\n"; 
			}

		}else
		{
			CallList	=	"찾을 수 없음";
		}
		
		document.getElementById("GetAbsenceCallList").value =CallList;	
}
//통화 내역 수 요청 함수
function GetCallCount(){
	var sKind		=	document.all.sKind.value;
	var kind	 =	"";
	switch (sKind)
	{
	case "1":	kind	 ="전체 통화내역 수 : "; break;
	case "2":	kind	 ="발신 통화내역 수 : "; break;
	case "3":	kind	 ="수신 통화내역 수 : "; break;
	case "4":	kind	 ="부재중 통화내역 수 : "; break;
	case "5":	kind	 ="실패 : "; break;
	case "6":	kind	 ="예약전송 : "; break;
	case "7":	kind	 ="핸드폰 : "; break;
	default : kind ="";
	
	}
		alert(kind + KTOpenAPIX.GetCallCount(sKind)  );
}
//통화 내역 리스트 요청 확장 함수
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
					case 2 : Kind = "발신" ; break;
					case 3 : Kind = "수신" ; break;
					case 4 : Kind = "부재중" ; break;
					case 5 : Kind = "실패" ; break;
					case 7 : Kind = "휴대폰" ; break;
					default : Kind = KTDCallDataArray[ i ].Kind; break;
				}
				
				CallList		+=	"발신 : "	+ KTDCallDataArray[ i ].Caller	+
							", 수신 : "		+ KTDCallDataArray[ i ].Callee	+
							", 수신날짜 : " + KTDCallDataArray[ i ].Date	+
							", 일련번호 : " + KTDCallDataArray[ i ].DBID	+
							", 결과 : "		+ KTDCallDataArray[ i ].Result	+ 
							", 통화내역 종류 : "		+  Kind	+ 	"\n"; 
			}
		}
		else
		{
			CallList	=	"찾을 수 없음";
		}
	document.getElementById("GetAbsenceCallList").value =CallList;	
}
//통화 내역 상세 정보 확장 함수
function GetCall(){
	
	var CallsDBID = document.all.CallsDBID.value;
	var	KTDCallData  =	KTOpenAPIX.GetCallEx(CallsDBID);

if( KTDCallData != null && KTDCallData != undefined )
		{	
			var Kind = "";

				switch(KTDCallData.Kind)
				{
					case 2 : Kind = "발신" ; break;
					case 3 : Kind = "수신" ; break;
					case 4 : Kind = "부재중" ; break;
					case 5 : Kind = "실패" ; break;
					case 7 : Kind = "휴대폰" ; break;
					default : Kind = KTDCallData.Kind; break;
				}

			alert(		"발신 : "		+ KTDCallData.Caller	+	
				", 수신 : "		+ KTDCallData.Callee	+
				", 수신날짜 : " + KTDCallData.Date		+
				", 일련번호 : " + KTDCallData.DBID		+
				", 결과 : "		+ KTDCallData.Result  + 
				", 통화내역 종류  : "		+  Kind
						);	
		}else
		{
			alert("찾을 수 없음");
		}
}
//통화 내역 삭제 요청 함수
function DelCall()
{
	var	result		=	500;
	var CallsDBID = document.all.CallsDBID.value;
	result = KTOpenAPIX.DelCall(CallsDBID);
	if (result == "0")
	{
		alert("서버 요청 실패(이미 발송된 경우 포함)");
	}else if (result =="200")
	{	
		alert("서버로 요청 성공함")
	}else if (result =="2000")
	{	
		alert("로그인 되어 있지 않음")
	}else {
		alert(result)	
	}

GetCallList();
}

//부재중 문자 내역 리스트 요청 함수
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
						"발신 : "			+ KTDCallDataArray[ i ].Caller	 +
						", 수신 : "			+ KTDCallDataArray[ i ].Callee	 +
						", 수신날짜 : "		+ KTDCallDataArray[ i ].Date	 +
						", 일련번호 : "		+ KTDCallDataArray[ i ].DBID	 +
						", 문자메시지 : "	+ KTDCallDataArray[ i ].Message  +
						", 결과 : "			+ KTDCallDataArray[ i ].Result  + "\n";
										
				}	
				
		}else{
			GetSmsList = "찾을 수 없음"
		}
		document.getElementById("GetAbsenceSmsList").value =GetSmsList;	
}

//문자 내역 수 요청 함수
function GetSmsCount()
{
	var SmssKind = document.all.SmssKind.value;
	alert(KTOpenAPIX.GetSmsCount(SmssKind));
}

//문자 내역 리스트 요청 확장 함수
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
					case 2 : Kind = "발신" ; break;
					case 3 : Kind = "수신" ; break;
					case 4 : Kind = "부재중" ; break;
					case 5 : Kind = "실패" ; break;
					case 7 : Kind = "휴대폰" ; break;
					default : Kind = KTDCallDataArray[ i ].Kind; break;
				}
				
				GetSmsList		+=	"발신 : "		+	KTDCallDataArray[ i ].Caller	+
							", 수신 : "			+	KTDCallDataArray[ i ].Callee	+
							", 수신날짜 : "		+	KTDCallDataArray[ i ].Date		+
							", 일련번호 : "		+	KTDCallDataArray[ i ].DBID		+
							", 문자메세지 : "	+	KTDCallDataArray[ i ].Message	+
							", 결과 : "			+	KTDCallDataArray[ i ].Result	+ 
							", 통화내역 종류 : "			+	Kind	+  "\n"; 
			}
		}
		else
		{
			GetSmsList	=	"찾을 수 없음";
		}

		document.getElementById("GetAbsenceSmsList").value =GetSmsList;	
}

//문자 내역 상세 정보 확장 함수
function GetSms()
{
	var SmssDBID = document.all.SmssDBID.value;
	var	KTDCallData  =	KTOpenAPIX.GetSmsEx(SmssDBID);
		
		if( KTDCallData != null && KTDCallData != undefined )
		{
				var Kind = "";

				switch(KTDCallData.Kind)
				{
					case 2 : Kind = "발신" ; break;
					case 3 : Kind = "수신" ; break;
					case 4 : Kind = "부재중" ; break;
					case 5 : Kind = "실패" ; break;
					case 7 : Kind = "휴대폰" ; break;
					default : Kind = KTDCallData.Kind; break;
				}

			alert(		"발신 : "			+	KTDCallData.Caller	+
						", 수신 : "			+	KTDCallData.Callee	+
						", 수신날짜 : "		+	KTDCallData.Date	+
						", 일련번호 : "		+	KTDCallData.DBID	+
						", 문자메세지 : "	+	KTDCallData.Message +
						", 결과 : "			+	KTDCallData.Result  + 
						", 통화내역 종류 : "			+	Kind
								);					
		}else{
			alert("찾을 수 없음");
		}
}

//문자 내역 삭제 함수
function DelSms()
{
	var	result		=	500;
	var SmssDBID = document.all.SmssDBID.value;
	result = KTOpenAPIX.DelSms(SmssDBID);
	if (result == "0")
	{
		alert("서버 요청 실패");
	}else if (result =="200")
	{	
		alert("서버로 요청 성공함");
	}else if (result =="2000")
	{	
		alert("로그인 되어 있지 않음");
	}else {
		alert(result);
	}
	GetSmsList();
}
// 통화 메모 수 요청 함수
function GetCallMemoCount(){
		var cCmType = document.all.cCmType.value;
		var type ="";
		if (cCmType == "1")
		{
			type="개인 통화 메모 갯수 : ";
		}else if (cCmType == "2")
		{
			type="공유 통화 메모 갯수 : ";
		}
		alert(type + KTOpenAPIX.GetCallMemoCount(cCmType));
}

//통화 메모 내역 리스트 요청 함수
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
				MemoList		+=	"발신 : "		+	KTDCallDataArray[ i ].Caller	+
							", 수신 : "			+	KTDCallDataArray[ i ].Callee	+
							", 통화내역일련번호 : "		+	KTDCallDataArray[ i ].CLDBID 		+
							", 통화메모일련번호 : "		+	KTDCallDataArray[ i ].DBID 		+
							", 발신자이름 : "		+	KTDCallDataArray[ i ].CallName 	+
							", 통화메모 : "	+	KTDCallDataArray[ i ].Memo 	+
							", 메모그룹 : "	+	KTDCallDataArray[ i ].MemoGroup 	+
							", 진행상태 : "	+	KTDCallDataArray[ i ].ProgGroup 	+
							", 수신날짜 : "	+	KTDCallDataArray[ i ].Date 	+
							", 메모타입 : "	+	KTDCallDataArray[ i ].Type 	+
							", 최종수정시간 : "			+	KTDCallDataArray[ i ].UDate 	+ "\n"; 
			}
		}
		else
		{
			MemoList	=	"찾을 수 없음";
		}

		document.getElementById("GetCallMemoCount").value =MemoList;	
}

//통화 메모 내역 요청 함수 
function GetCallMemo(){
		var cCmType = document.all.cCmType.value;
		var MemosDBID  = document.all.MemosDBID  .value;
		var KTDCallData = KTOpenAPIX.GetCallMemo(cCmType, MemosDBID);
		var type="";
		if( KTDCallData != null && KTDCallData != undefined )
		{
		
		if (KTDCallData.Type == "1")
		{
			type = "개인 통화 메모";
		}else if (KTDCallData.Type == "2")
		{
			type = "공유 통화 메모";
		}
		alert(		"발신 : "			+	KTDCallData.Caller	+
				", 수신 : "			+	KTDCallData.Callee	+
				", 통화내역일련번호 : "		+	KTDCallData.CLDBID 	+
				", 통화메모일련번호 : "		+	KTDCallData.DBID	+
				", 발신자 : "		+	KTDCallData.CallName  +
				", 통화메모 : "	+	KTDCallData.Memo  +
				", 메모그룹 : "	+	KTDCallData.MemoGroup  +
				", 진행상태 : "	+	KTDCallData.ProgGroup  +
				", 수신날짜 : "	+	KTDCallData.Date  +
				", 통화내역 고유 일련번호 : "	+	KTDCallData.LogicID  +
				", 타입 : "	+	type  +
				", 최종수정시간 : "			+	KTDCallData.UDate  
					);					
		}else{
			alert("찾을 수 없음");
		}

}

//통화 메모 추가 요청 함수
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
			alert("로그인 되어 있지 않거나 요청 에러");
		}else{		
			alert("신규 통화 메모 일련번호 :" + result)
		}
 	GetCallMemoList();
}
//통화 메모 수정 요청 함수
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
	case 200: alert("통화메모 수정 요청 성공");break;
	case 2000: alert("로그인 되어 있지 않음");break;
	case 4300: alert("통화 메모 이름 길이가 30byte를 초과"); break;
	case 4301: alert("통화 메모 길이가 256byte를 초과"); break;
	default : alert("신규 통화 메모 일련번호" + result );	
	}
	GetCallMemoList();
}
//통와 메모 삭제 요청 함수
function DelCallMemo(){
		var	result		=	500;
		var cCmType = document.all.cCmType.value;
		var MemosDBID = document.all.MemosDBID.value;
		result	=	KTOpenAPIX.DelCallMemo(cCmType, MemosDBID);
		switch (result)
		{
		case 200: alert("통화메모 삭제 요청 성공");break;
		case 2000: alert("로그인 되어 있지 않음");break;
		case 4300: alert("통화 메모 이름 길이가 30byte를 초과"); break;
		case 4301: alert("통화 메모 길이가 256byte를 초과"); break;
		default : alert("신규 통화 메모 일련번호" + result );	
		}
		GetCallMemoList();
}

//주소록 그룹 리스트 요청 함수
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
				GroupList		+=	"그룹 일련번호 : "		+	KTDCallDataArray[ i ].DBID 	+
							", 그룹 타입 : "			+	KTDCallDataArray[ i ].Type 	+
							", 그룹 이름 : "		+	KTDCallDataArray[ i ].Name  		+
							", 부모 그룹 일련번호 : "		+	KTDCallDataArray[ i ].PDBID  		+
							 "\n"; 
			}
		}
		else
		{
			GroupList	=	"찾을 수 없음";
		}

		document.getElementById("GetAddressGroupList").value =GroupList;	
}

//주소록 그룹 정보 가져오기 요청 함수
function GetAddressGroup(){
		var cAgType = document.all.cAgType.value;
		var GroupsDBID = document.all.GroupsDBID.value;
		var	KTDCallData	=	KTOpenAPIX.GetAddressGroup(cAgType, GroupsDBID);
	
	if( KTDCallData != null && KTDCallData != undefined )
		{
		alert (	
			"그룹 일련 번호 : " + KTDCallData.DBID	+	
			", 그룹 타입" + KTDCallData.Type 	+
			", 그룹 이름" + KTDCallData.Name 	+
			", 부모 그룹 일련번호" + KTDCallData.PDBID 
		);		
		}else {
			alert ("찾을 수 없음")
		}
}

//신규 주소록 그룹 추가 함수
function NewAddressGroup(){
		var result = 500;
		var cAgType = document.all.cAgType.value;
		var sAgName = document.all.sAgName.value;
		var sPDBID = document.all.sPDBID.value;
		
		result	=	KTOpenAPIX.NewAddressGroup(cAgType, sAgName, sPDBID);

		if (result == "0")
		{
			alert("신규 주소록 그룹 추가 요청 실패");
		}else{
			alert("신규 주소록 그룹 일련번호 : " + result);
		}
		GetAddressGroupList();
}

//주소록 그룹 수정 함수
function EditAddressGroup(){
		var result = 500;
		var cAgType = document.all.cAgType.value;
		var sPDBID = document.all.sPDBID.value;
		var sAgName = document.all.sAgName.value;
		var GroupsDBID = document.all.GroupsDBID.value;

		result	=	KTOpenAPIX.EditAddressGroup(cAgType, sAgName, sPDBID, GroupsDBID);
		switch (result)
		{
		case 0: alert("주소록 변경 요청 실패"); break;
		case 200: alert("주소록 그룹 변경 요청 성공"); break;
		case 2000: alert("로그인 되어 있지 않음"); break;
		case 4400: alert("변경할 주소록 그룹 이름이 없음"); break;
		case 4401: alert("주소록 그룹을 찾을 수 없음"); break;
		case 4402: alert("이미 동일한 주소록 그룹을 가지고 있음"); break;
		case 4301: alert("통화 메모 길이가 256 byte를 초과"); break;
		default: alert(result);	
		}
		GetAddressGroupList();
}

// 주소록 그룹 삭제 함수
function DelAddressGroup(){
		var result = 500;
		var cAgType = document.all.cAgType.value;
		var GroupsDBID = document.all.GroupsDBID.value;
		result =  KTOpenAPIX.DelAddressGroup(cAgType, GroupsDBID);
		switch (result)
		{
		case 0: alert("주소록 그룹 삭제 요청 실패"); break;
		case 200: alert("주소록 그룹 삭제 요청 성공"); break;
		case 2000: alert("로그인 되어 있지 않음"); break;
		case 4401: alert("주소록 그룹을 찾을 수 없음"); break;
		default: alert(result);	
		}
		GetAddressGroupList();
}

//주소록 사용자 리스트 요청 함수
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
				DataList		+=	"일련번호 : "		+	KTDCallDataArray[ i ].DBID 	+
							", 이름 : "			+	KTDCallDataArray[ i ].Name         	+
							", 타입 : "		+	KTDCallDataArray[ i ].Type           		+
							", 집주소 : "		+	KTDCallDataArray[ i ].Address       		+
							", 생년월일 : "		+	KTDCallDataArray[ i ].BirthDay      		+
							", 음력0, 양력1 : "		+	KTDCallDataArray[ i ].BirthDay      		+
							", 업무 : "		+	KTDCallDataArray[ i ].Business      		+
							", 회사명 : "		+	KTDCallDataArray[ i ].CompanyName   		+
							", 부서명 : "		+	KTDCallDataArray[ i ].Department    		+
							", 이메일 : "		+	KTDCallDataArray[ i ].Email         		+
							", 관심사 : "		+	KTDCallDataArray[ i ].Favorite      		+
							", 팩스 : "		+	KTDCallDataArray[ i ].FaxNum        		+
							", 집 전화 : "		+	KTDCallDataArray[ i ].HomeNum       		+
							", 추천인 : "		+	KTDCallDataArray[ i ].Keyman        		+
							", 인물메모 : "		+	KTDCallDataArray[ i ].Memo          		+
							", 만난상황 : "		+	KTDCallDataArray[ i ].MetChance     		+
							", 핸드폰번호 : "		+	KTDCallDataArray[ i ].MobileNum     		+
							", 회사전화 : "		+	KTDCallDataArray[ i ].OfficeNum     		+
							", 직책 : "		+	KTDCallDataArray[ i ].Position      		+
							", 우편번호 : "		+	KTDCallDataArray[ i ].ZipCode       		+
							"\n"; 
			}
		}
		else
		{
			DataList =	 "찾을 수 없음"
		}

		document.getElementById("GetAddressDataList").value =DataList;	
		
}

//주소록 사용자 정보 요청 함수
function GetAddressData(){
			var DatacAgType = document.all.DatacAgType.value;
			var DatasDBID  = document.all.DatasDBID.value;

			var	KTDAddressData   =	KTOpenAPIX.GetAddressData(DatacAgType, DatasDBID);
						
						alert(
						"일련번호: "	+	KTDAddressData.DBID	+
						", 이름: "			+	KTDAddressData.Name	+
						", 타입: "	+	KTDAddressData. Type	+
						", 집주소: "		+	KTDAddressData.Address	+
						", 생년월일: "		+	KTDAddressData.BirthDay	+
						", 음력0,양력1: "	+	KTDAddressData.BirthType	+
						", 업무: "			+	KTDAddressData.Business	+
						", 회사명: "		+	KTDAddressData.CompanyName	 	+
						", 부서명: "		+	KTDAddressData.Department	+
						", 이메일주소: "	+	KTDAddressData.Email	+
						", 관심사: "		+	KTDAddressData.Favorite	+
						", 팩스번호: "		+	KTDAddressData.FaxNum	+
						", 집전화번호: "	+	KTDAddressData.HomeNum	+
						", 추천인: "		+	KTDAddressData.Keyman	+
						", 인물메모: "		+	KTDAddressData.Memo	+
						", 만난상황: "		+	KTDAddressData.MetChance	+
						", 핸드폰: "	+	KTDAddressData.MobileNum	+
						", 회사전화: "	+	KTDAddressData.OfficeNum	+
						", 직책: "			+	KTDAddressData.Position	+
						", 우편번호: "		+	KTDAddressData.ZipCode
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

//주소록 사용자 추가 요청 함수
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
				alert("주소록 사용자 추가 서버 요청 실패");
			}else{
				alert("일련번호" + result);
			}
			GetAddressDataList()
}

//주소록 사용자 수정 함수
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
	case 0: alert("주소록 사용자 수정 서버 요청 실패"); break;
	case 200: alert("주소록 그룹 변경 요청 선공"); break;
	case 2000: alert("로그인 되어 있지 않음"); break;
	case 4401: alert("주소록 그룹을 찾을 수 없음"); break;
	case 4500: alert("주소록 사용자 이름이 없음"); break;
	case 4502: alert("주소록 사용자 전화번호가 하나도 존재하지 않음"); break;
	default:alert(result);
	}
	GetAddressDataList()
}
//주소록 사용자 삭제 함수
function DelAddressData(){
			var result	=	500;
			var DatacAgType = document.all.DatacAgType.value;
			var DatasPDBID  = document.all.DatasPDBID.value;
			var DatasDBID  = document.all.DatasDBID.value;
		result   =	KTOpenAPIX.DelAddressData( DatacAgType,DatasPDBID, DatasDBID);
	
	switch (result)
	{
	case 0: alert("주소록 사용자 수정 서버 요청 실패"); break;
	case 200: alert("주소록 그룹 삭제 요청 선공"); break;
	case 2000: alert("로그인 되어 있지 않음"); break;
	case 4500: alert("주소록 사용자 이름이 없음"); break;
	case 4501: alert("주소록 사용자를 찾을 수 없음"); break;
	default:alert(result);
	}
	GetAddressDataList()
}