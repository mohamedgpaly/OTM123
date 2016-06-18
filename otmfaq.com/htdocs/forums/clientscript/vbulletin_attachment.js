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
function vB_Attachment(A,E){var F=YAHOO.util.Dom.getElementsByClassName("uploadlaunchlink");if(F.length){for(var C=0;C<F.length;C++){YAHOO.util.Event.on(F[C],"click",this.attachmanage,this,true)}}var D=YAHOO.util.Dom.getElementsByClassName("uploadlaunchbutton");if(D.length){for(var C=0;C<D.length;C++){YAHOO.util.Event.on(D[C],"click",this.attachmanage,this,true);YAHOO.util.Dom.setStyle(D[C],"display","")}}var B=YAHOO.util.Dom.getElementsByClassName("uploadlaunchfallback");if(B.length){for(var C=0;C<B.length;C++){YAHOO.util.Dom.setStyle(B[C],"display","none")}}this.assetobj=null;this.content=E;this.attachments=new Array();this.menu_contents=new Array();this.windows=new Array();this.editor=null;this.listobjid=A;if(this.content.editorid==""){for(editorid in vB_Editor){if(YAHOO.lang.hasOwnProperty(vB_Editor,editorid)){this.editor=vB_Editor[editorid];break}}}else{this.editor=(this.content.editorid?vB_Editor[this.content.editorid]:null)}}vB_Attachment.prototype.attachmanage=function(A){if(typeof (A)!="undefined"){YAHOO.util.Event.stopEvent(A)}if(AJAX_Compatible&&this.content.enhanced==1){this.open_window("newattachment.php?"+SESSIONURL+"do=assetmanager&"+PHP.unhtmlspecialchars(this.content.values)+"&contenttypeid="+this.content.contenttypeid+"&poststarttime="+this.content.poststarttime+"&posthash="+this.content.posthash+"&insertinline="+this.content.insertinline,725,520,this.content.contenttypeid+"_"+this.content.posthash)}else{this.open_window("newattachment.php?"+SESSIONURL+PHP.unhtmlspecialchars(this.content.values)+"&contenttypeid="+this.content.contenttypeid+"&poststarttime="+this.content.poststarttime+"&posthash="+this.content.posthash,480,480,this.content.posthash)}};vB_Attachment.prototype.popup_exists=function(){if(this.editor&&((typeof this.editor.popups.attach!="undefined"&&this.editor.popups.attach!=null)||(!this.editor.popupmode&&typeof this.editor.buttons.attach!="undefined"&&this.editor.buttons.attach!=null))){return true}else{return false}};vB_Attachment.prototype.add=function(E,D,B,C,A){this.attachments[E]=new Array();this.attachments[E]={html:D,filename:B,filesize:C,imgpath:A};this.update_list()};vB_Attachment.prototype.remove=function(A){if(typeof this.attachments[A]!="undefined"){this.attachments[A]=null;this.update_list()}};vB_Attachment.prototype.has_attachments=function(){for(var A in this.attachments){if(YAHOO.lang.hasOwnProperty(this.attachments,A)&&this.attachments[A]!=null){return true}}return false};vB_Attachment.prototype.reset=function(){this.attachments=new Array();this.update_list()};vB_Attachment.prototype.build_list=function(B,G){var C=YAHOO.util.Dom.get(B);if(C){var A=C.getElementsByTagName("li");if(A[G]!=null){while(A[G].nextSibling){C.removeChild(A[G].nextSibling)}}var E=0;for(var F in this.attachments){if(!YAHOO.lang.hasOwnProperty(this.attachments,F)){continue}var D=string_to_node(this.attachments[F]["html"]);D.attachmentid=F;C.appendChild(D);E++}if(E){YAHOO.util.Dom.removeClass("uploaddisplay","hidden");YAHOO.util.Dom.removeClass(C,"hidden")}else{YAHOO.util.Dom.addClass("uploaddisplay","hidden");YAHOO.util.Dom.addClass(C,"hidden")}}};vB_Attachment.prototype.update_list=function(){this.build_list(this.listobjid,1);this.build_list(this.listobjid+"_list2",0);if(this.popup_exists()){this.editor.build_attachments_popup(this.editor.popupmode?this.editor.popups.attach:this.editor.buttons.attach,this.editor.buttons.attach)}};vB_Attachment.prototype.open_window=function(B,C,A,D){if(typeof (this.windows[D])!="undefined"&&this.windows[D].closed==false){this.windows[D].focus()}else{this.windows[D]=openWindow(B,C,A,"Attach"+D)}return this.windows[D]};