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
function vB_AJAX_BlogCalendar(B,C,E,D,A){this.xml_sender=null;this.calobj=C;this.varname=B;this.userid=A;this.init=function(F){if(AJAX_Compatible&&(typeof vb_disable_ajax=="undefined"||vb_disable_ajax<2)&&C){if(nextmonth=YAHOO.util.Dom.get("vb_blogcalendar_nextmonth")){nextmonth.style.cursor=pointer_cursor;YAHOO.util.Event.on("vb_blogcalendar_nextmonth","click",this.next_month,this,true)}if(prevmonth=YAHOO.util.Dom.get("vb_blogcalendar_prevmonth")){prevmonth.style.cursor=pointer_cursor;YAHOO.util.Event.on("vb_blogcalendar_prevmonth","click",this.prev_month,this,true)}if(F){new vBCollapseFactory(this.calobj)}}};this.handle_ajax_response=function(G){if(G.responseXML){var I=fetch_object(this.objid);var F=G.responseXML.getElementsByTagName("error");if(F.length){alert(F[0].firstChild.nodeValue)}else{var H=G.responseXML.getElementsByTagName("calendar")[0].firstChild.nodeValue;if(H!=""){fetch_object(this.calobj).innerHTML=H;this.init(true)}}}};this.prev_month=function(I){YAHOO.util.Event.stopEvent(I);var G=YAHOO.util.Dom.get("vb_blogcalendar_prevmonth");var H=YAHOO.util.Dom.getAttribute(G,"prevmonth");var F=YAHOO.util.Dom.getAttribute(G,"prevyear");this.swap_month(H,F);return false};this.next_month=function(I){YAHOO.util.Event.stopEvent(I);var G=YAHOO.util.Dom.get("vb_blogcalendar_nextmonth");var H=YAHOO.util.Dom.getAttribute(G,"nextmonth");var F=YAHOO.util.Dom.getAttribute(G,"nextyear");this.swap_month(H,F);return false};this.swap_month=function(G,F){YAHOO.util.Connect.asyncRequest("POST","blog_ajax.php?do=calendar",{success:this.handle_ajax_response,failure:vBulletin_AJAX_Error_Handler,timeout:vB_Default_Timeout,scope:this},SESSIONURL+"securitytoken="+SECURITYTOKEN+"&do=calendar&m="+G+"&ajax=1&y="+F+(typeof this.userid!="undefined"?"&u="+this.userid:""))};this.init()};