/*======================================================================*\
|| #################################################################### ||
|| # vBulletin 4.1.1 Patch Level 1
|| # ---------------------------------------------------------------- # ||
|| # Copyright �2000-2011 vBulletin Solutions Inc. All Rights Reserved. ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # ---------------- VBULLETIN IS NOT FREE SOFTWARE ---------------- # ||
|| # http://www.vbulletin.com | http://www.vbulletin.com/license.html # ||
|| #################################################################### ||
\*======================================================================*/
function vB_Facebook(A){this.config=A;this.ids={loginform_id:"navbar_loginform",loginform_usernameid:"navbar_username",loginform_passwordid:"navbar_password",fb_usernameid:"facebookusername",fb_passwordid:"facebookpassword",fb_associateid:"facebookassociate",invisibles:[{id:"fb_headerbox"},{id:"fbregbox"}],publishcheckboxid:"fb_dopublish",loginbtns:[{id:"fb_loginbtn"},{id:"fb_regloginbtn"},{id:"fb_getconnected"}]};this.loginperms=this.get_config("autoreg")?{perms:"email"}:{perms:"email,user_about_me,user_activities,user_birthday,user_interests,user_likes,user_website,user_location,user_work_history"};FB.init({appId:this.get_config("appid"),status:true,cookie:true,xfbml:false});if(LOGGEDIN&&this.get_config("connected")){this.prepare_publishtofacebook()}this.register_loginbtns();this.make_visible()}vB_Facebook.prototype.get_config=function(A){if(typeof (this.config)=="undefined"||typeof (this.config[A])=="undefined"){return false}else{return this.config[A]}};vB_Facebook.prototype.register_loginbtns=function(){for(var C=0;C<this.ids.loginbtns.length;C++){var D=fetch_object(this.ids.loginbtns[C].id);if(D){YAHOO.util.Event.on(D,"click",this.handle_fbLogin,this,true)}}var B=fetch_object(this.ids.fb_usernameid);var E=fetch_object(this.ids.fb_passwordid);var A=fetch_object(this.ids.fb_associateid);if(B&&E&&A){YAHOO.util.Event.on(A,"click",this.login_and_associate,this,true);this.fb_username_listener=new YAHOO.util.KeyListener(B,{keys:13},{fn:this.handle_associate_keypress,scope:this,correctScope:true});this.fb_username_listener.enable();this.fb_password_listener=new YAHOO.util.KeyListener(E,{keys:13},{fn:this.handle_associate_keypress,scope:this,correctScope:true});this.fb_password_listener.enable()}};vB_Facebook.prototype.make_visible=function(){for(var A=0;A<this.ids.invisibles.length;A++){var B=fetch_object(this.ids.invisibles[A].id);if(B){YAHOO.util.Dom.removeClass(B,"hidden")}}};vB_Facebook.prototype.do_fbRedirect=function(){var A=window.top.location.href.replace(/#.*/,"");var B=(window.top.location.search.substring(1)?"&":"?");window.top.location=A+B+"dofbredirect=1"+window.top.location.hash};vB_Facebook.prototype.handle_fbLogin=function(A){YAHOO.util.Event.stopEvent(A);if(FB.getSession(true)){this.do_fbRedirect()}else{FB.login(function(B){if(B.session){vBfb.do_fbRedirect()}},this.loginperms)}};vB_Facebook.prototype.handle_associate_keypress=function(B,A){YAHOO.util.Event.preventDefault(A[1]);this.login_and_associate()};vB_Facebook.prototype.login_and_associate=function(){var D=fetch_object(this.ids.loginform_id);var A=fetch_object(this.ids.loginform_usernameid);var C=fetch_object(this.ids.loginform_passwordid);var B=fetch_object(this.ids.fb_usernameid);var E=fetch_object(this.ids.fb_passwordid);if(A&&B){A.value=B.value}if(C&&E){C.value=E.value}if(D){D.onsubmit();D.submit()}};vB_Facebook.prototype.register_logout=function(){FB.logout(vBfb.do_fbRedirect)};vB_Facebook.prototype.prepare_publishtofacebook=function(){var E=fetch_object(this.ids.publishcheckboxid);var C=E;while(C&&C.tagName.toLowerCase()!="form"){C=C.parentNode}this.editFormEl=C;if(this.editFormEl){if(this.editFormEl.id=="form_widget_comments"&&E.type!="radio"){this.onSubmitEvent=this.editFormEl.sbutton.onclick;this.editFormEl.sbutton.onclick=null;YAHOO.util.Event.on(this.editFormEl.sbutton,"click",this.check_publishingperms_cmscomment,this,true)}else{this.onSubmitEvent=this.editFormEl.onsubmit;this.editFormEl.onsubmit=null;YAHOO.util.Event.on(this.editFormEl,"submit",this.check_publishingperms,this,true);var B=this.editFormEl.getElementsByTagName("input");for(var D=0;D<B.length;D++){var A=B[D];if(A.type=="submit"||A.type=="image"){YAHOO.util.Event.on(A,"click",this.track_submitbutton,this,true)}}}}};vB_Facebook.prototype.track_submitbutton=function(A){this.btnClicked=YAHOO.util.Event.getTarget(A)};vB_Facebook.prototype.is_pubCbChecked=function(){var A=fetch_object(this.ids.publishcheckboxid);return(typeof (A)!="undefined"&&A.checked==1)};vB_Facebook.prototype.check_publishingperms=function(A){YAHOO.util.Event.stopEvent(A);if(this.is_pubCbChecked()&&(this.btnClicked.name=="sbutton"||this.btnClicked.id=="save_btn"||this.btnClicked.id=="apply_btn")){FB.login(function(B){vBfb.handle_submit_override()},{perms:"publish_stream"})}else{this.handle_submit_override()}};vB_Facebook.prototype.check_publishingperms_cmscomment=function(A){if(this.is_pubCbChecked()){FB.login(function(B){vBfb.onSubmitEvent()},{perms:"publish_stream"})}else{this.onSubmitEvent()}};vB_Facebook.prototype.handle_submit_override=function(){if(typeof (this.onSubmitEvent)=="undefined"||!this.onSubmitEvent||this.onSubmitEvent.call(this.editFormEl)){var A=document.createElement("input");A.setAttribute("type","hidden");A.setAttribute("name",this.btnClicked.name);A.setAttribute("value",this.btnClicked.value);this.editFormEl.appendChild(A);this.editFormEl.submit()}};