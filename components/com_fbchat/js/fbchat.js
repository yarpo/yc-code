var initialize = function ($) {
    $.jfbcchat = function () { 
    	//Path per l'entrypoint REST AJAX con risposta JSON Format  
    	//Prova a cercare per il tag base per i subfolders...
    	mosConfig_live_site = $('base').attr('href'); 
    	if(mosConfig_live_site){
    		 var g = mosConfig_live_site + "index2.php?option=com_fbchat&format=raw";
    	     var gImages = mosConfig_live_site + 'components/com_fbchat/';
    	} else {
    		//Detect del percorso da JS non affidabile e valido solo per root folder
    		 var mosConfig_live_site = window.location.host; 
    		 var g = "http://" + mosConfig_live_site + "/index2.php?option=com_fbchat&format=raw";
    	     var gImages = "http://" + mosConfig_live_site + '/components/com_fbchat/';
    	} 
        var t = {};
        var s = {};
        var f = {};
        var T = "";
        var r = 0;
        var o = 0;
        var J = true;                               
        var l = 0;
        var u = false;
        var U;
        var X = 3000;
        var F = 12000;
        var K = 3000;
        var h = 1;
        var R = 0;
        var n = 0;
        var audio = 0;
        var showBackLink = false;
        var my_username;
        var powered = '<a target="blank" href="http://www.2punti.eu">Design by 2Punti</a>';
        $("<div/>").attr("id", "jfbcchat_base").appendTo($("body"));

        function optionsTooltip(ac, Z) {
            if ($("#jfbcchat_tooltip").length > 0) {
                $("#jfbcchat_tooltip .jfbcchat_tooltip_content").html(Z)
            } else {
                $("body").append('<div id="jfbcchat_tooltip"><div class="jfbcchat_tooltip_content">' + Z + "</div></div>")
            }
            var ab = $("#" + ac).offset();
            var Y = $("#" + ac).width();
            var mixed = $("#jfbcchat_tooltip").width();
            $("#jfbcchat_tooltip").css("bottom", 29).css("left", (ab.left + Y) - mixed + 12).css("display", "block");
            if (u) {
                $("#jfbcchat_tooltip").css("position", "absolute");
                $("#jfbcchat_tooltip").css("top", parseInt($(window).height()) - parseInt($("#jfbcchat_tooltip").css("bottom")) - parseInt($("#jfbcchat_tooltip").height()) + $(window).scrollTop() + "px")
            }
        }
        function sendMessage(Z, Y, mixed) {
            if (Z.keyCode == 13 && Z.shiftKey == 0) {
                message = $(Y).val();
                message = message.replace(/^\s+|\s+$/g, "");
                $(Y).val("");
                $(Y).css("height", "18px");
                $("#jfbcchat_user_" + mixed + "_popup .jfbcchat_tabcontenttext").css("height", "200px");
                $(Y).css("overflow-y", "hidden");
                $(Y).focus();
                if (message != "") {
                    $.post(g, {
                        to: mixed,
                        message: message,
                        entrypoint: 'send'
                    }, function (ab) {
                        if (ab) {
                            $("#jfbcchat_userlist_" + mixed).trigger("addmessage", [message, "1", "1", ab]);
                            $("#jfbcchat_user_" + mixed + "_popup .jfbcchat_tabcontenttext").scrollTop($("#jfbcchat_user_" + mixed + "_popup .jfbcchat_tabcontenttext")[0].scrollHeight)
                        }
                        h = 1;
                        if (K > X) {
                            K = X;
                            clearTimeout(U);
                            U = setTimeout(function () {
                                ajaxReceive();
                            }, X)
                        }
                    })
                }
                return false
            }
        } 
        function sendStatusMessage(Z, Y, btnSave) {
       
            if (Z.keyCode == 13 && Z.shiftKey == 0) {
                message = $(Y).val();
                if (message == "") {
                	message = "-";
                } 
                    $.post(g, {
                        statusmessage: message,
                        entrypoint: 'send'
                    }, function (mixed) {
                        $(Y).blur()
                    }) 
                return false
            } else if(btnSave){ 
            	$("#setstatusmessage").css({'color':'red'})
            	message = $(Y).val(); 
                if (message == "") {
                	message = "-";
                } 
                    $.post(g, {
                        statusmessage: message,
                        entrypoint: 'send'
                    }, function (mixed) {
                        $(Y).blur();  
                         $("#setstatusmessage").css({'color':'#287197'})
                    }) 
            }
        }
       
        function Popup(ab, mixed, ac) {
            var Z = mixed.clientHeight;
            var Y = 94;
            if (Y > Z) {
                Z = Math.max(mixed.scrollHeight, Z);
                if (Y) {
                    Z = Math.min(Y, Z)
                }
                if (Z > mixed.clientHeight) {
                    $(mixed).css("height", Z + 4 + "px");
                    $("#jfbcchat_user_" + ac + "_popup .jfbcchat_tabcontenttext").css("height", 218 - (Z + 4) + "px")
                }
            } else {
                $(mixed).css("overflow-y", "auto")
            }
            $("#jfbcchat_user_" + ac + "_popup .jfbcchat_tabcontenttext").scrollTop($("#jfbcchat_user_" + ac + "_popup .jfbcchat_tabcontenttext")[0].scrollHeight)
        }
        function statusClassOp() {
            $("#jfbcchat_optionsbutton_popup .busy").css("text-decoration", "none");
            $("#jfbcchat_optionsbutton_popup .invisible").css("text-decoration", "none");
            $("#jfbcchat_optionsbutton_popup .available").css("text-decoration", "none");
            $("#jfbcchat_userstab_icon").removeClass("jfbcchat_user_available2");
            $("#jfbcchat_userstab_icon").removeClass("jfbcchat_user_busy2");
            $("#jfbcchat_userstab_icon").removeClass("jfbcchat_user_invisible2")
        }
        function postStatus(Y) {
            $.post(g, {
                status: Y,
                entrypoint: 'send'
            }, function (Z) {})
        }
        function usersTabSt(Y) {
            o = 1;
            statusClassOp();
            $("#jfbcchat_userstab_icon").addClass("jfbcchat_user_invisible2");
            if (Y != 1) {
                postStatus("offline")
            }
            $("#jfbcchat_userstab_popup").removeClass("jfbcchat_tabopen");
            $("#jfbcchat_userstab").removeClass("jfbcchat_userstabclick").removeClass("jfbcchat_tabclick");
            $("#jfbcchat_optionsbutton_popup").removeClass("jfbcchat_tabopen");
            $("#jfbcchat_optionsbutton").removeClass("jfbcchat_tabclick");
            C("buddylist", "0");
            $("#jfbcchat_userstab_text").html("Offline")
        }
        function initializeOptionsDiv() {
        	$.ajax({ 
                url: g,
                data: "getParams=true",
                type: "post",
                cache: false,
                dataType: "json",
                success: function (response) { 
                	var showBacklink = response.paramslist.showbacklink; 
                	if(showBacklink == '1')
                		var backLinkClName = 'jfbcchat_backlink';
                	else
                		var backLinkClName = 'jfbcchat_backlink_novisible';
                    var resContainer = $("<span/>").addClass("jfbcchat_tab").addClass(backLinkClName);
                    $("#jfbcchat_optionsbutton").before(resContainer);
                    $('<a target="_blank" href="http://www.2punti.eu">&nbsp;&nbsp;&nbsp;</a>').appendTo(resContainer);
                    
                }
        	});
        	
        	
            $("<span/>").attr("id", "jfbcchat_optionsbutton").addClass("jfbcchat_tab").addClass("jfbcchat_optionsimages").appendTo($("#jfbcchat_base"));
            $("<div/>").attr("id", "jfbcchat_optionsbutton_popup").addClass("jfbcchat_tabpopup").css("display", "none").html('<div class="jfbcchat_userstabtitle">' + chatoptionstitle + '</div><div class="jfbcchat_tabcontent" style="background-image: url(' 
            		+ gImages + 'images/tabbottomoptions.gif);padding:5px;height:156px;"><strong>' + mystatus 
            		+ '</strong><br/><textarea class="jfbcchat_statustextarea"></textarea><span style="float:left" class="jfbcchat_user_available"></span><span class="jfbcchat_optionsstatus available">' 
            		+ available + '</span><span class="jfbcchat_optionsstatus2 jfbcchat_user_busy"></span><span class="jfbcchat_optionsstatus busy">' + statooccupato 
            		+ '</span><span class="jfbcchat_optionsstatus2 jfbcchat_user_invisible"></span><span class="jfbcchat_optionsstatus invisible">' + statoinvisibile 
            		+ '</span><br clear="all"/><div class="fbchat_testaudio">Test Audio</div>').appendTo($("body"));
            
            $("#jfbcchat_optionsbutton_popup .available").click(function (Y) {
                statusClassOp();
                $("#jfbcchat_userstab_icon").addClass("jfbcchat_user_available2");
                $(this).css("text-decoration", "underline");
                postStatus("available")
            });
            $("#jfbcchat_optionsbutton_popup .busy").click(function (Y) {
                statusClassOp();
                $("#jfbcchat_userstab_icon").addClass("jfbcchat_user_busy2");
                $(this).css("text-decoration", "underline");
                postStatus("busy")
            });
            $("#jfbcchat_optionsbutton_popup .invisible").click(function (Y) {
                statusClassOp();
                $("#jfbcchat_userstab_icon").addClass("jfbcchat_user_invisible2");
                $(this).css("text-decoration", "underline");
                postStatus("invisible")
            });
            $("#jfbcchat_optionsbutton_popup .jfbcchat_statustextarea").keydown(function (Y) {
                return sendStatusMessage(Y, this)
            });
            $("#jfbcchat_optionsbutton").mouseover(function () {
                if (!$("#jfbcchat_optionsbutton_popup").hasClass("jfbcchat_tabopen")) {
                    if (r == 0) {
                        optionsTooltip("jfbcchat_optionsbutton", chatoptionstitle)
                    } else {
                        optionsTooltip("jfbcchat_optionsbutton", pleaselogin)
                    }
                }
                $(this).addClass("jfbcchat_tabmouseover")
            });
            $("#jfbcchat_optionsbutton").mouseout(function () {
                $(this).removeClass("jfbcchat_tabmouseover");
                $("#jfbcchat_tooltip").css("display", "none")
            });
            $("#jfbcchat_optionsbutton").click(function () {
                if (r == 0) {
                    if (o == 1) {
                        o = 0;
                        $("#jfbcchat_userstab_text").html(chionline);
                        ajaxReceive();
                        $("#jfbcchat_optionsbutton_popup .available").click()
                    }
                    $("#jfbcchat_tooltip").css("display", "none");
                    $("#jfbcchat_optionsbutton_popup").css("left", $("#jfbcchat_optionsbutton").position().left - 171).css("bottom", "24px");
                    $(this).toggleClass("jfbcchat_tabclick");
                    $("#jfbcchat_optionsbutton_popup").toggleClass("jfbcchat_tabopen");
                    $("#jfbcchat_optionsbutton").toggleClass("jfbcchat_optionsimages_click");
                    $("#jfbcchat_userstab_popup").removeClass("jfbcchat_tabopen");
                    $("#jfbcchat_userstab").removeClass("jfbcchat_userstabclick").removeClass("jfbcchat_tabclick");
                    C("buddylist", "0")
                }
            });
            $("#jfbcchat_optionsbutton_popup .jfbcchat_userstabtitle").click(function () {
                $("#jfbcchat_optionsbutton").click()
            });
            $("#jfbcchat_optionsbutton_popup .jfbcchat_userstabtitle").mouseenter(function () {
                $(this).addClass("jfbcchat_chatboxtabtitlemouseover2")
            });
            $("#jfbcchat_optionsbutton_popup .jfbcchat_userstabtitle").mouseleave(function () {
                $(this).removeClass("jfbcchat_chatboxtabtitlemouseover2")
            })
        }
        function c() {
            var Y = "";
            $("#jfbcchat_chatboxes_wide span").each(function () {
                var mixed = $(this).attr("id").substr(15);
                var Z = 0;
                if ($("#jfbcchat_user_" + mixed + " .jfbcchat_tabalert").length > 0) {
                    Z = parseInt($("#jfbcchat_user_" + mixed + " .jfbcchat_tabalert").html())
                }
                Y += mixed + "|" + Z + ","
            });
            Y = Y.slice(0, -1);
            C("activeChatboxes", Y)
        }
        function getHandlers(ae, ab, Y, ad, Z, ac) {
            if ($("#jfbcchat_user_" + ae).length > 0) {
                if (!$("#jfbcchat_user_" + ae).hasClass("jfbcchat_tabclick")) {
                    if (Z != 1) {
                        if (T != "") {
                            $("#jfbcchat_user_" + T + "_popup").removeClass("jfbcchat_tabopen");
                            $("#jfbcchat_user_" + T).removeClass("jfbcchat_tabclick").removeClass("jfbcchat_usertabclick");
                            T = ""
                        }
                        if (($("#jfbcchat_user_" + ae).offset().left < ($("#jfbcchat_chatboxes").offset().left + $("#jfbcchat_chatboxes").width())) && ($("#jfbcchat_user_" + ae).offset().left - $("#jfbcchat_chatboxes").offset().left) >= 0) {
                            $("#jfbcchat_user_" + ae).click()
                        } else {
                            $(".jfbcchat_tabalert").css("display", "none");
                            var mixed = 800;
                            if (e("initialize") == 1 || e("updatesession") == 1) {
                                mixed = 0
                            }
                            $("#jfbcchat_chatboxes").scrollTo("#jfbcchat_user_" + ae, mixed, function () {
                                $("#jfbcchat_user_" + ae).click();
                                positioner();
                                tabAlertHtml()
                            })
                        }
                    }
                }
                positioner();
                return
            }
            $("#jfbcchat_chatboxes_wide").width($("#jfbcchat_chatboxes_wide").width() + 152);
            q();
            if (ab.length > 14) {
                shortname = ab.substr(0, 14) + "..."
            } else {
                shortname = ab
            }
            if (ab.length > 24) {
                longname = ab.substr(0, 24) + "..."
            } else {
                longname = ab
            }
            $("<span/>").attr("id", "jfbcchat_user_" + ae).addClass("jfbcchat_tab").html('<div style="float:left">' + shortname + "</div>").appendTo($("#jfbcchat_chatboxes_wide"));
            $("#jfbcchat_user_" + ae).append('<div class="jfbcchat_closebox_bottom_status jfbcchat_' + Y + '"></div>');
            $("#jfbcchat_user_" + ae).append('<div class="jfbcchat_closebox_bottom"></div>');
            $("#jfbcchat_user_" + ae + " .jfbcchat_closebox_bottom").mouseenter(function () {
                $(this).addClass("jfbcchat_closebox_bottomhover")
            });
            $("#jfbcchat_user_" + ae + " .jfbcchat_closebox_bottom").mouseleave(function () {
                $(this).removeClass("jfbcchat_closebox_bottomhover")
            });
            $("#jfbcchat_user_" + ae + " .jfbcchat_closebox_bottom").click(function () {
                $("#jfbcchat_user_" + ae + "_popup").remove();
                $("#jfbcchat_user_" + ae).remove();
                if (T == ae) {
                    T = "";
                    C("openChatboxId", "")
                }
                $("#jfbcchat_chatboxes_wide").width($("#jfbcchat_chatboxes_wide").width() - 152);
                $("#jfbcchat_chatboxes").scrollTo("-=152px");
                q();
                c()
            });
            $("<div/>").attr("id", "jfbcchat_user_" + ae + "_popup").addClass("jfbcchat_tabpopup").css("display", "none").html('<div class="jfbcchat_tabtitle"><div class="jfbcchat_name">' + longname + '</div></div><div class="jfbcchat_tabsubtitle">' + ad + '</div><div class="jfbcchat_tabcontent"><div class="jfbcchat_tabcontenttext"></div><div class="jfbcchat_tabcontentinput"><textarea class="jfbcchat_textarea" ></textarea></div></div>').appendTo($("body"));
            $("#jfbcchat_user_" + ae + "_popup .jfbcchat_textarea").keydown(function (af) {
                return sendMessage(af, this, ae)
            });
            $("#jfbcchat_user_" + ae + "_popup .jfbcchat_textarea").keyup(function (af) {
                return Popup(af, this, ae)
            });
            $("#jfbcchat_user_" + ae + "_popup .jfbcchat_tabtitle").append('<div class="jfbcchat_closebox"></div><br clear="all"/>');
            $("#jfbcchat_user_" + ae + "_popup .jfbcchat_tabtitle .jfbcchat_closebox").mouseenter(function () {
                $(this).addClass("jfbcchat_chatboxmouseoverclose");
                $("#jfbcchat_user_" + ae + "_popup .jfbcchat_tabtitle").removeClass("jfbcchat_chatboxtabtitlemouseover")
            });
            $("#jfbcchat_user_" + ae + "_popup .jfbcchat_tabtitle .jfbcchat_closebox").mouseleave(function () {
                $(this).removeClass("jfbcchat_chatboxmouseoverclose");
                $("#jfbcchat_user_" + ae + "_popup .jfbcchat_tabtitle").addClass("jfbcchat_chatboxtabtitlemouseover")
            });
            $("#jfbcchat_user_" + ae + "_popup .jfbcchat_tabtitle .jfbcchat_closebox").click(function () {
                $("#jfbcchat_user_" + ae + "_popup").remove();
                $("#jfbcchat_user_" + ae).remove();
                if (T == ae) {
                    T = "";
                    C("openChatboxId", "")
                }
                $("#jfbcchat_chatboxes_wide").width($("#jfbcchat_chatboxes_wide").width() - 152);
                $("#jfbcchat_chatboxes").scrollTo("-=152px");
                q();
                c()
            });
            $("#jfbcchat_user_" + ae + "_popup .jfbcchat_tabtitle").click(function () {
                $("#jfbcchat_user_" + ae).click()
            });
            $("#jfbcchat_user_" + ae + "_popup .jfbcchat_tabtitle").mouseenter(function () {
                $(this).addClass("jfbcchat_chatboxtabtitlemouseover")
            });
            $("#jfbcchat_user_" + ae + "_popup .jfbcchat_tabtitle").mouseleave(function () {
                $(this).removeClass("jfbcchat_chatboxtabtitlemouseover")
            });
            $("#jfbcchat_user_" + ae).mouseenter(function () {
                $(this).addClass("jfbcchat_tabmouseover");
                $("#jfbcchat_user_" + ae + " div").addClass("jfbcchat_tabmouseovertext")
            });
            $("#jfbcchat_user_" + ae).mouseleave(function () {
                $(this).removeClass("jfbcchat_tabmouseover");
                $("#jfbcchat_user_" + ae + " div").removeClass("jfbcchat_tabmouseovertext")
            });
            $("#jfbcchat_user_" + ae).click(function () {
                if ($("#jfbcchat_user_" + ae + " .jfbcchat_tabalert").length > 0) {
                    $("#jfbcchat_user_" + ae + " .jfbcchat_tabalert").remove();
                    c()
                }
                if ($(this).hasClass("jfbcchat_tabclick")) {
                    $(this).removeClass("jfbcchat_tabclick").removeClass("jfbcchat_usertabclick");
                    $("#jfbcchat_user_" + ae + "_popup").removeClass("jfbcchat_tabopen");
                    $("#jfbcchat_user_" + ae + " .jfbcchat_closebox_bottom").removeClass("jfbcchat_closebox_bottom_click");
                    T = "";
                    C("openChatboxId", "")
                } else {
                    if (T != "") {
                        $("#jfbcchat_user_" + T + "_popup").removeClass("jfbcchat_tabopen");
                        $("#jfbcchat_user_" + T).removeClass("jfbcchat_tabclick").removeClass("jfbcchat_usertabclick");
                        $("#jfbcchat_user_" + T + " .jfbcchat_closebox_bottom").removeClass("jfbcchat_closebox_bottom_click");
                        T = ""
                    }
                    if (($("#jfbcchat_user_" + ae).offset().left - $("#jfbcchat_chatboxes").offset().left) < 0) {
                        $("#jfbcchat_chatboxes").scrollTo("#jfbcchat_user_" + ae);
                        positioner()
                    }
                    $("#jfbcchat_user_" + ae + "_popup").css("left", $("#jfbcchat_user_" + ae).position().left - 62).css("bottom", "24px");
                    $(this).addClass("jfbcchat_tabclick").addClass("jfbcchat_usertabclick");
                    $("#jfbcchat_user_" + ae + "_popup").addClass("jfbcchat_tabopen");
                    $("#jfbcchat_user_" + ae + " .jfbcchat_closebox_bottom").addClass("jfbcchat_closebox_bottom_click");
                    C("openChatboxId", ae);
                    T = ae;
                    if ($("#jfbcchat_user_" + ae + "_popup .jfbcchat_tabcontenttext").html() == "" && e("initialize") != 1) {
                        E(ae)
                    }
                    if (u) {
                        $("#jfbcchat_user_" + T + "_popup").css("position", "absolute");
                        $("#jfbcchat_user_" + T + "_popup").css("top", parseInt($(window).height()) - parseInt($("#jfbcchat_user_" + T + "_popup").css("bottom")) - parseInt($("#jfbcchat_user_" + T + "_popup").height()) + $(window).scrollTop() + "px")
                    }
                }
                $("#jfbcchat_user_" + ae + "_popup .jfbcchat_tabcontenttext").scrollTop($("#jfbcchat_user_" + ae + "_popup .jfbcchat_tabcontenttext")[0].scrollHeight);
                $("#jfbcchat_user_" + ae + "_popup .jfbcchat_textarea").focus()
            });
            if (Z != 1) {
                $("#jfbcchat_user_" + ae).click()
            }
            c();
            if ($("#jfbcchat_user_" + ae + "_popup .jfbcchat_tabcontenttext").html() == "" && e("initialize") != 1) {
                E(ae)
            }
        }
        function E(Y) {
			//alert(Y)
            $.ajax({
                async: false, 
                url: g,
                data: {
                    chatbox: Y,
                    entrypoint: 'receive'
                },
                type: "post",
                cache: false,
                dataType: "json",
                success: function (ab) {
                    if (ab) {
                        var userYBox = $("#jfbcchat_user_" + Y + "_popup .jfbcchat_tabcontenttext").html("");
                        var Z = "";
                        var mixed = $("#jfbcchat_userlist_" + Y).triggerHandler("getname");
                        $.each(ab, function (ac, ad) {
                            if (ac == "messages") {
                                $.each(ad, function (af, ae) {
                                    if (ae.self == 1) {
                                        fromname = my_username
                                    } else {
                                        fromname = mixed
                                    }
                                    ae.message = ae.message.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;");
									var chatBoxId = 'jfbcchat_message_' + ae.id;
									var chatBoxMsg = $('<div class="jfbcchat_chatboxmessage" id="' + chatBoxId + 
										'"><span class="jfbcchat_chatboxmessagefrom"><strong>' + fromname + 
										'</strong>:&nbsp;&nbsp;</span><span class="jfbcchat_chatboxmessagecontent">' + ae.message + 
										//'(tlumaczenie 1)' + 
										"</span></div>");
                                    userYBox.append(chatBoxMsg);
									translate(chatBoxId);
                                })
                            }
                        });
                        userYBox.scrollTop(userYBox[0].scrollHeight);
                    }
                }
            })
        }

		function translate(id)
		{
			var box = $('#' + id);
			var msg = box.find('.jfbcchat_chatboxmessagecontent').text();
			google.language.detect(msg, function(detection) { 
				debug('lang: ' + detection.language); 
				if (detection && !detection.error && detection.language != 'pl')
				{
					google.language.translate(msg, detection.language, 'pl', 
						function(result) 
						{ 
							if (result && !result.error)
							{
								box.html(box.html() + fCreateTranslationBox(result.translation));
							}
						});
				}
			});
		}
		
		function fCreateTranslationBox( content )
		{
			var c = [];
			c.push('<span class="jfbcchat_ycAddedTranslation">');
			c.push(content);
			c.push('</span>');
			return c.join('');
		}

        function listaUtenti(ab, Z, Y, mixed) { 
        	//Nel caso non ci sia uno stato specificato si procede con uno di default preso dal file lingua
        	if (mixed == '')
        		mixed = defaultstatus;
        	//Se l'utente � gi� presente nella lista in quanto il DOM node � presente per quell'id aggiorniamo solo lo stato
            if ($("#jfbcchat_userlist_" + ab).length > 0) {
                $("#jfbcchat_user_" + ab + " .jfbcchat_closebox_bottom_status").removeClass("jfbcchat_available");
                $("#jfbcchat_user_" + ab + " .jfbcchat_closebox_bottom_status").removeClass("jfbcchat_busy");
                $("#jfbcchat_user_" + ab + " .jfbcchat_closebox_bottom_status").removeClass("jfbcchat_offline");
                $("#jfbcchat_user_" + ab + " .jfbcchat_closebox_bottom_status").addClass("jfbcchat_" + Y);
                if ($("#jfbcchat_user_" + ab + "_popup").length > 0) {
                    $("#jfbcchat_user_" + ab + "_popup .jfbcchat_tabsubtitle").html(mixed)
                }
                 $("#jfbcchat_userlist_" + ab).remove()
            } else { 
            	//Se l'utente � un nuovo connesso invece riproduciamo il suono di arrivo, ma SOLO dopo l'initialize
            	if(e('initialize') != 1 && audio == 1)
            		$(msgNotify).trigger('onClient');
            }
            if (Z.length > 24) {
                longname = Z.substr(0, 24) + "..."
            } else {
                longname = Z
            }
            $("<div />").attr("id", "jfbcchat_userlist_" + ab).addClass("jfbcchat_userlist").html('<span class="jfbcchat_userscontentname">' + longname + '</span><span class="jfbcchat_userscontentdot jfbcchat_' + Y + '"></span>').appendTo($("#jfbcchat_userstab_popup .jfbcchat_tabcontent .jfbcchat_userscontent .jfbcchat_userslist_" + Y));
            $("#jfbcchat_userlist_" + ab).mouseover(function () {
                $(this).addClass("jfbcchat_userlist_hover")
            });
            $("#jfbcchat_userlist_" + ab).mouseout(function () {
                $(this).removeClass("jfbcchat_userlist_hover")
            });
            $("#jfbcchat_userlist_" + ab).click(function () {
                getHandlers(ab, Z, Y, mixed)
            });
            $("#jfbcchat_userlist_" + ab).dblclick(function () {
                getHandlers(ab, Z, Y, mixed, 1)
            });
            $("#jfbcchat_userlist_" + ab).bind("addmessage", function (ag, ac, af, ae, ad) {
                getHandlers(ab, Z, Y, mixed, 1, 1);
                if (af == 1) {
                    fromname = my_username
                } else {
                    fromname = Z
                }
                ac = ac.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;");
                if ($("#jfbcchat_message_" + ad).length > 0) {} 
				else 
				{
					var chatBoxId = 'jfbcchat_message_' + ad;
					var chatBoxMsg = $('<div class="jfbcchat_chatboxmessage" id="' + chatBoxId + '">' + 
						'<span class="jfbcchat_chatboxmessagefrom"><strong>' + fromname + 
						'</strong>:&nbsp;&nbsp;</span><span class="jfbcchat_chatboxmessagecontent">' + ac 
						//+ "(tlumaczenie2)" +
						+ "</span></div>");
					$("#jfbcchat_user_" + ab + "_popup .jfbcchat_tabcontenttext")
						.append(chatBoxMsg);
					translate(chatBoxId);
                }
                if (T != ab && ae != 1) {
                    tabAlert(ab, 1, 1)
                }
            });
            $("#jfbcchat_userlist_" + ab).bind("getname", function (ac) {
                return Z
            })
        }
        function tabAlert(mixed, Y, Z) {
            $("#jfbcchat_userstab_popup .jfbcchat_tabcontent ." + mixed).dblclick();
            if (Z == 1) {
                if ($("#jfbcchat_user_" + mixed + " .jfbcchat_tabalert").length > 0) {
                    Y = parseInt($("#jfbcchat_user_" + mixed + " .jfbcchat_tabalert").html()) + parseInt(Y);
                    //L'evento onMessage va triggato sia quando si invia un messaggio con l'alert gi� aperto e si incrementa il counter..
                    if(audio == 1)
                    	$(msgNotify).trigger("onMessage");
                }
            }
            if (Y == 0) {
                $("#jfbcchat_user_" + mixed + " .jfbcchat_tabalert").remove()
            } else {
                if ($("#jfbcchat_user_" + mixed + " .jfbcchat_tabalert").length > 0) {
                    $("#jfbcchat_user_" + mixed + " .jfbcchat_tabalert").html(Y)
                } else {
                    $("<div/>").css("top", "-5px").addClass("jfbcchat_tabalert").html(Y).appendTo($("#jfbcchat_user_" + mixed))
                    //...sia quando si inietta per la prima volta nel DOM
                    if(audio == 1)
                    	$(msgNotify).trigger("onMessage");
                }
            }
            c();
            tabAlertHtml()
        }
        function initializeDom() {
            $("<span/>").attr("id", "jfbcchat_userstab").addClass("jfbcchat_tab").html('<span id="jfbcchat_userstab_icon"></span><span id="jfbcchat_userstab_text" style="float:left">' + chionline + '</span>').appendTo($("#jfbcchat_base"));
            $("<div/>").attr("id", "jfbcchat_userstab_popup").addClass("jfbcchat_tabpopup").css("display", "none").html('<div class="jfbcchat_userstabtitle">' + chionline + spacer + '<span class="poweredby">' + powered + '</span></div><div class="jfbcchat_tabsubtitle"><a class="jfbcchat_gooffline">'+gooffline+'</a></div><div class="jfbcchat_tabcontent" style="background-image: url(' + gImages + 'images/tabbottomwhosonline.gif);height:200px;padding-top:5px;padding-bottom:5px;"><div class="jfbcchat_userscontent"><div class="jfbcchat_userslist_available"></div><div class="jfbcchat_userslist_busy"></div><div class="jfbcchat_userslist_away"></div><div class="jfbcchat_userslist_offline"></div></div>').appendTo($("body"));
            $("#jfbcchat_userstab_popup .jfbcchat_gooffline").click(function () {
                usersTabSt()
            });
            $("#jfbcchat_userstab_popup .jfbcchat_userstabtitle").click(function () {
                $("#jfbcchat_userstab").click()
            });
            $("#jfbcchat_userstab_popup .jfbcchat_userstabtitle").mouseenter(function () {
                $(this).addClass("jfbcchat_chatboxtabtitlemouseover2")
            });
            $("#jfbcchat_userstab_popup .jfbcchat_userstabtitle").mouseleave(function () {
                $(this).removeClass("jfbcchat_chatboxtabtitlemouseover2")
            });
            $("#jfbcchat_userstab").mouseover(function () {
                $(this).addClass("jfbcchat_tabmouseover")
            });
            $("#jfbcchat_userstab").mouseout(function () {
                $(this).removeClass("jfbcchat_tabmouseover")
            });
            $("#jfbcchat_userstab").click(function () {
                if (o == 1) {
                    o = 0;
                    $("#jfbcchat_userstab_text").html(chionline);
                    ajaxReceive();
                    $("#jfbcchat_optionsbutton_popup .available").click()
                }
                $("#jfbcchat_optionsbutton_popup").removeClass("jfbcchat_tabopen");
                $("#jfbcchat_optionsbutton").removeClass("jfbcchat_tabclick");
                if ($(this).hasClass("jfbcchat_tabclick")) {
                    C("buddylist", "0")
                } else {
                    C("buddylist", "1")
                }
                $("#jfbcchat_userstab_popup").css("left", $("#jfbcchat_userstab").position().left + 16).css("bottom", "24px");
                $(this).toggleClass("jfbcchat_tabclick").toggleClass("jfbcchat_userstabclick");
                $("#jfbcchat_userstab_popup").toggleClass("jfbcchat_tabopen")
            })
        }
        function q() {
            var Z = $(window).width();
            if (Z < 520) {
                Z = 520
            }
            $("#jfbcchat_base").css("width", Z - 31);
            var ab = 0;
            if (!$("#jfbcchat_chatbox_right").hasClass("jfbcchat_chatbox_lr")) {
                ab = 19
            }
            if ($("#jfbcchat_chatboxes_wide").width() <= ($("#jfbcchat_base").width() - 226 - ab - 75)) {
                $("#jfbcchat_chatboxes").css("width", $("#jfbcchat_chatboxes_wide").width());
                $("#jfbcchat_chatboxes").scrollTo("0px", 0)
            } else {
                var ac = $("#jfbcchat_chatboxes").width();
                $("#jfbcchat_chatboxes").css("width", Math.floor(($("#jfbcchat_base").width() - 226 - ab - 75) / 152) * 152);
                var Y = $("#jfbcchat_chatboxes").width();
                if (ac != Y) {
                    $("#jfbcchat_chatboxes").scrollTo("-=152px", 0)
                }
            }
            $("#jfbcchat_optionsbutton_popup").css("left", $("#jfbcchat_optionsbutton").position().left - 171).css("bottom", "24px");
            $("#jfbcchat_userstab_popup").css("left", $("#jfbcchat_userstab").position().left + 16).css("bottom", "24px");
            if (T != "") {
                if (($("#jfbcchat_user_" + T).offset().left < ($("#jfbcchat_chatboxes").offset().left + $("#jfbcchat_chatboxes").width())) && ($("#jfbcchat_user_" + T).offset().left - $("#jfbcchat_chatboxes").offset().left) >= 0) {
                    $("#jfbcchat_user_" + T + "_popup").css("left", $("#jfbcchat_user_" + T).position().left - 62).css("bottom", "24px")
                } else {
                    $("#jfbcchat_user_" + T + "_popup").removeClass("jfbcchat_tabopen");
                    $("#jfbcchat_user_" + T).removeClass("jfbcchat_tabclick").removeClass("jfbcchat_usertabclick");
                    var mixed = (($("#jfbcchat_user_" + T).offset().left - $("#jfbcchat_chatboxes_wide").offset().left)) - ((Math.floor(($("#jfbcchat_chatboxes").width() / 152)) - 1) * 152);
                    $("#jfbcchat_chatboxes").scrollTo(mixed, 0, function () {
                        $("#jfbcchat_user_" + T).click()
                    })
                }
            }
            tabAlertHtml();
            positioner();
            if (u) {
                base()
            }
        }
        function tabAlertHtml() {
            $("#jfbcchat_chatbox_left .jfbcchat_tabalertlr").html("0");
            $("#jfbcchat_chatbox_right .jfbcchat_tabalertlr").html("0");
            $("#jfbcchat_chatbox_left .jfbcchat_tabalertlr").css("display", "none");
            $("#jfbcchat_chatbox_right .jfbcchat_tabalertlr").css("display", "none");
            $(".jfbcchat_tabalert").each(function () {
                if (($(this).parent().offset().left < ($("#jfbcchat_chatboxes").offset().left + $("#jfbcchat_chatboxes").width())) && ($(this).parent().offset().left - $("#jfbcchat_chatboxes").offset().left) >= 0) {
                    $(this).css("display", "block");
                    $(this).css("left", $(this).parent().offset().left + $(this).parent().width() - 30)
                } else {
                    $(this).css("display", "none");
                    if (($(this).parent().offset().left - $("#jfbcchat_chatboxes").offset().left) >= 0) {
                        var Y = $("#jfbcchat_chatbox_right").offset().left + $("#jfbcchat_chatbox_right").width() - 30;
                        $("#jfbcchat_chatbox_right .jfbcchat_tabalertlr").css("left", Y);
                        $("#jfbcchat_chatbox_right .jfbcchat_tabalertlr").html(parseInt($("#jfbcchat_chatbox_right .jfbcchat_tabalertlr").html()) + parseInt($(this).html()));
                        $("#jfbcchat_chatbox_right .jfbcchat_tabalertlr").css("display", "block")
                    } else {
                        var Y = $("#jfbcchat_chatbox_left").offset().left + $("#jfbcchat_chatbox_left").width() - 22;
                        $("#jfbcchat_chatbox_left .jfbcchat_tabalertlr").css("left", Y);
                        $("#jfbcchat_chatbox_left .jfbcchat_tabalertlr").html(parseInt($("#jfbcchat_chatbox_left .jfbcchat_tabalertlr").html()) + parseInt($(this).html()));
                        $("#jfbcchat_chatbox_left .jfbcchat_tabalertlr").css("display", "block")
                    }
                }
            })
        }
        function positioner() {
            var mixed = 0;
            var ab = 0;
            var Y = 0;
            if ($("#jfbcchat_chatbox_right").hasClass("jfbcchat_chatbox_right_last")) {
                ab = 1
            }
            if ($("#jfbcchat_chatbox_right").hasClass("jfbcchat_chatbox_lr")) {
                Y = 1
            }
            if ($("#jfbcchat_chatboxes").scrollLeft() == 0) {
                $("#jfbcchat_chatbox_left").unbind("click", d);
                $("#jfbcchat_chatbox_left .jfbcchat_tabtext").html("0");
                $("#jfbcchat_chatbox_left").addClass("jfbcchat_chatbox_left_last");
                mixed++
            } else {
                var Z = Math.floor($("#jfbcchat_chatboxes").scrollLeft() / 152);
                $("#jfbcchat_chatbox_left").bind("click", d);
                $("#jfbcchat_chatbox_left .jfbcchat_tabtext").html(Z);
                $("#jfbcchat_chatbox_left").removeClass("jfbcchat_chatbox_left_last")
            }
            if (($("#jfbcchat_chatboxes").scrollLeft() + $("#jfbcchat_chatboxes").width()) == $("#jfbcchat_chatboxes_wide").width()) {
                $("#jfbcchat_chatbox_right").unbind("click", k);
                $("#jfbcchat_chatbox_right .jfbcchat_tabtext").html("0");
                $("#jfbcchat_chatbox_right").addClass("jfbcchat_chatbox_right_last");
                mixed++
            } else {
                var Z = Math.floor(($("#jfbcchat_chatboxes_wide").width() - ($("#jfbcchat_chatboxes").scrollLeft() + $("#jfbcchat_chatboxes").width())) / 152);
                $("#jfbcchat_chatbox_right").bind("click", k);
                $("#jfbcchat_chatbox_right .jfbcchat_tabtext").html(Z);
                $("#jfbcchat_chatbox_right").removeClass("jfbcchat_chatbox_right_last")
            }
            if (mixed == 2) {
                $("#jfbcchat_chatbox_right").addClass("jfbcchat_chatbox_lr");
                $("#jfbcchat_chatbox_left").addClass("jfbcchat_chatbox_lr")
            } else {
                $("#jfbcchat_chatbox_right").removeClass("jfbcchat_chatbox_lr");
                $("#jfbcchat_chatbox_left").removeClass("jfbcchat_chatbox_lr")
            }
            if ((!$("#jfbcchat_chatbox_right").hasClass("jfbcchat_chatbox_right_last") && ab == 1) || ($("#jfbcchat_chatbox_right").hasClass("jfbcchat_chatbox_right_last") && ab == 0) || (!$("#jfbcchat_chatbox_right").hasClass("jfbcchat_chatbox_lr") && Y == 1) || ($("#jfbcchat_chatbox_right").hasClass("jfbcchat_chatbox_lr") && Y == 0)) {
                q()
            }
        }
        function p(Z) {
            if (T != "") {
                $("#jfbcchat_user_" + T + "_popup").removeClass("jfbcchat_tabopen");
                $("#jfbcchat_user_" + T).removeClass("jfbcchat_tabclick").removeClass("jfbcchat_usertabclick")
            }
            $(".jfbcchat_tabalert").css("display", "none");
            var Y = 800;
            if (e("initialize") == 1 || e("updatesession") == 1) {
                Y = 0
            }
            $("#jfbcchat_chatboxes").scrollTo(Z, Y, function () {
                if (T != "") {
                    if (($("#jfbcchat_user_" + T).offset().left < ($("#jfbcchat_chatboxes").offset().left + $("#jfbcchat_chatboxes").width())) && ($("#jfbcchat_user_" + T).offset().left - $("#jfbcchat_chatboxes").offset().left) >= 0) {
                        $("#jfbcchat_user_" + T).click()
                    } else {
                        T = ""
                    }
                }
                tabAlertHtml();
                positioner()
            })
        }
        function d() {
            p("-=152px")
        }
        function k() {
            p("+=152px")
        }
        function b(Y, Z) {
            t[Y] = Z
        }
        function e(Y) {
            if (t[Y]) {
                return t[Y]
            } else {
                return ""
            }
        }
        function M(Y, Z) {
            f[Y] = Z
        }
        function O(Y) {
            if (f[Y]) {
                return f[Y]
            } else {
                return ""
            }
        }
        function C(Y, Z) {
            s[Y] = Z;
            if (e("initialize") != 1 && e("updatesession") != 1 && O("updatingsession") != 1) {
                R = 1;
                clearTimeout(U);
                U = setTimeout(function () {
                    ajaxReceive()
                }, 1000)
            }
        }
        function getProp(Y, Z) {
            if (s[Y]) {
                return s[Y]
            } else {
                return ""
            }
        }
        function ajaxReceive() {
            for (vars in s) {
                t["sessionvars[" + vars + "]"] = s[vars];
            }
            t.entrypoint = 'receive';
            
            if (R == 1) {
                b("updatesession", "1");
                R = 0
            }
            t.timestamp = n;
            var Y = ""; 
            $.ajax({ 
                url: g,
                data: t,
                type: "post",
                cache: false,
                dataType: "json",
                success: function (mixed) {
                    if (mixed) {
                        var Z = 0;
						var id2translate = [];
                        $.each(mixed, function (ab, ac) {
                        	//***Settings dei parametri***// 
                        	if(ab == "paramslist") {  
                        			X = ac.chatrefresh * 1000;
                        			K = ac.chatrefresh * 1000; 
                        			audio = ac.audioenabled;  
                        	}
                            if (ab == "buddylist") {
                                $.each(ac, function (ae, ad) { 
                                    listaUtenti(ad.id, ad.name, ad.status, ad.message)
                                })
                            }
                            if (ab == "my_username" && ab != '' && ab != 'undefined'){
                            	my_username = ac; 
                            }
                            if (ab == "loggedout") {
                                $("#jfbcchat_optionsbutton").addClass("jfbcchat_optionsimages_exclamation");
                                $("#jfbcchat_userstab").hide();
                                $("#jfbcchat_chatboxes").hide();
                                $("#jfbcchat_chatbox_left").hide();
                                $("#jfbcchat_chatbox_right").hide();
                                $("#jfbcchat_optionsbutton_popup").hide();
                                $("#jfbcchat_userstab_popup").hide();
                                $(".jfbcchat_tabopen").css("cssText", "display: none !important;");
                                if (T != "") {
                                    $("#jfbcchat_user_" + T + "_popup").hide();
                                    T = ""
                                }
                                r = 1
                            }
                            if (ab == "userstatus") {
                                $.each(ac, function (ad, ae) {
                                    if (ad == "message") {
                                        $("#jfbcchat_optionsbutton_popup .jfbcchat_statustextarea").val(ae)
                                    }
                                    if (ad == "status") {
                                        if (ae == "offline") {
                                            usersTabSt(1)
                                        } else {
                                            statusClassOp();
                                            $("#jfbcchat_userstab_icon").addClass("jfbcchat_user_" + ae + "2");
                                            $("#jfbcchat_optionsbutton_popup ." + ae).css("text-decoration", "underline")
                                        }
                                    }
                                })
                            }
                            if (ab == "initialize") {
                                $.each(ac, function (ad, af) {
                                    if (ad == "buddylist") {
                                        if (af == 1) {
                                            $("#jfbcchat_userstab").click()
                                        }
                                    }
                                    if (ad == "activeChatboxes") {
                                        var ag = af.split(/,/);
                                        for (i = 0; i < ag.length; i++) {
                                            var ae = ag[i].split(/\|/);
                                            $("#jfbcchat_userlist_" + ae[0]).dblclick();
                                            if (parseInt(ae[1]) > 0) {
                                                tabAlert(ae[0], ae[1], 0)
                                            }
                                        }
                                    }
                                    if (ad == "openChatboxId") {
                                        if (af != "") {
                                            $("#jfbcchat_userlist_" + af).click()
                                        }
                                    }
                                });
                                b("initialize", "0")
                            }
                            if (ab == "updatesession" && R != 1 && e("updatesession") != 1) {
                                M("updatingsession", "1");
                                $.each(ac, function (ad, ag) {
                                    if (ad == "buddylist") {
                                        if ((ag == 0 && $("#jfbcchat_userstab").hasClass("jfbcchat_tabclick")) || (ag == 1 && !($("#jfbcchat_userstab").hasClass("jfbcchat_tabclick")))) {
                                            $("#jfbcchat_userstab").click()
                                        }
                                    }
                                    if (ad == "activeChatboxes") {
                                        if (ag != ah) {
                                            var ai = new Array;
                                            var af = new Array;
                                            if (ag != "") {
                                                var ah = ag.split(/,/);
                                                for (i = 0; i < ah.length; i++) {
                                                    var ae = ah[i].split(/\|/);
                                                    ai[ae[0]] = ae[1]
                                                }
                                            }
                                            if (getProp("activeChatboxes") != "") {
                                                var ah = getProp("activeChatboxes").split(/,/);
                                                for (i = 0; i < ah.length; i++) {
                                                    var ae = ah[i].split(/\|/);
                                                    af[ae[0]] = ae[1]
                                                }
                                            }
                                            for (x in ai) {
                                                if ($("#jfbcchat_user_" + x).length > 0) {} else {
                                                    $("#jfbcchat_userlist_" + x).dblclick()
                                                }
                                            }
                                            for (y in af) {
                                                if (ai[y] == null) {
                                                    $("#jfbcchat_user_" + y + "_popup .jfbcchat_tabtitle .jfbcchat_closebox").click()
                                                }
                                            }
                                        }
                                    }
                                    if (ad == "openChatboxId") {
                                        if (ag != T) {
                                            if (T != "") {
                                                $("#jfbcchat_user_" + T).click()
                                            }
                                            if (ag != "") {
                                                $("#jfbcchat_user_" + ag).click()
                                            }
                                        }
                                    }
                                });
                                M("updatingsession", "0");
                                b("updatesession", "0")
                            }
                            if (ab == "messages") {
								
                                $.each(ac, function (af, ad) {
                                    n = ad.id;
                                    if (parseInt(T) == parseInt(ad.from)) {
                                        ++Z;
                                        var ae = $("#jfbcchat_userlist_" + ad.from).triggerHandler("getname");
                                        if (ad.self == 1) {
                                            fromname = my_username
                                        } else {
                                            fromname = ae
                                        }
                                        ad.message = ad.message.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;");
                                        if ($("#jfbcchat_message_" + ad.id).length > 0) {} 
										else 
										{
											id2translate.push("jfbcchat_message_" + ad.id);
                                            Y += ('<div class="jfbcchat_chatboxmessage" id="jfbcchat_message_' + ad.id + 
												'"><span class="jfbcchat_chatboxmessagefrom"><strong>' + fromname + 
												'</strong>:&nbsp;&nbsp;</span><span class="jfbcchat_chatboxmessagecontent">' + ad.message 
												// "(tlumaczenie 3) " 
												+ "</span></div>")
                                            //Observer pattern JQuery, lanciamo un evento custom per arrivo messaggi
                                            if(audio == 1)
                                            	$(msgNotify).trigger("onMessage");
                                        }
                                    } else {
                                        $("#jfbcchat_userlist_" + ad.from).trigger("addmessage", [ad.message, ad.self, ad.old, ad.id])
                                    }
                                });
                                h = 1;
                                K = X
                            }
                        });
                        if (T != "" && Z > 0) {
                            $("#jfbcchat_user_" + T + "_popup .jfbcchat_tabcontenttext").append(Y);
							
							for (var i=0; i < id2translate.length; i++)
							{
								debug('\t' + id2translate[i]);
								translate(id2translate[i]);
							}
                            $("#jfbcchat_user_" + T + "_popup .jfbcchat_tabcontenttext").scrollTop($("#jfbcchat_user_" + T + "_popup .jfbcchat_tabcontenttext")[0].scrollHeight)
                        }
                    }
                    b("initialize", "0");
                    b("updatesession", "0");
                    if (r != 1 && o != 1) {
                        h++;
                        if (h > 4) {
                            K *= 2;
                            h = 1
                        }
                        if (K > F) {
                            K = F
                        }
                        clearTimeout(U);
                        U = setTimeout(function () {
                            ajaxReceive();
                        }, K)
                    }
                }
            })
        }
        function startApp() {
            initializeOptionsDiv();
            initializeDom();
            $("<div/>").attr("id", "jfbcchat_chatbox_right").appendTo($("#jfbcchat_base"));
            $("<span/>").addClass("jfbcchat_tabtext").appendTo($("#jfbcchat_chatbox_right"));
            $("<span/>").css("top", "-5px").css("display", "none").addClass("jfbcchat_tabalertlr").appendTo($("#jfbcchat_chatbox_right"));
            $("#jfbcchat_chatbox_right").bind("click", k);
            $("#setstatusmessage").bind("click",function(){sendStatusMessage({}||null,$('textarea.jfbcchat_statustextarea'),true);});
            $("<div/>").attr("id", "jfbcchat_chatboxes").appendTo($("#jfbcchat_base"));
            $("<div/>").attr("id", "jfbcchat_chatboxes_wide").appendTo($("#jfbcchat_chatboxes"));
            $("<div/>").attr("id", "jfbcchat_chatbox_left").appendTo($("#jfbcchat_base"));
            $("<span/>").addClass("jfbcchat_tabtext").appendTo($("#jfbcchat_chatbox_left"));
            $("<span/>").css("top", "-5px").css("display", "none").addClass("jfbcchat_tabalertlr").appendTo($("#jfbcchat_chatbox_left"));
            $("#jfbcchat_chatbox_left").bind("click", d);
            q();
            positioner();
            $("#jfbcchat_chatbox_right").mouseover(function () {
                $(this).addClass("jfbcchat_chatbox_lr_mouseover")
            });
            $("#jfbcchat_chatbox_right").mouseout(function () {
                $(this).removeClass("jfbcchat_chatbox_lr_mouseover")
            });
            $("#jfbcchat_chatbox_left").mouseover(function () {
                $(this).addClass("jfbcchat_chatbox_lr_mouseover")
            });
            $("#jfbcchat_chatbox_left").mouseout(function () {
                $(this).removeClass("jfbcchat_chatbox_lr_mouseover")
            });
            $(window).bind("resize", q);
            b("buddylist", "1");
            b("initialize", "1");
            b("updatesession", "0");
            if (typeof document.body.style.maxHeight === "undefined") {
                u = true;
                $("#jfbcchat_base").css("position", "absolute");
                $("#jfbcchat_tooltip").css("position", "absolute");
                $("#jfbcchat_userstab_popup").css("position", "absolute");
                $("#jfbcchat_optionsbutton_popup").css("position", "absolute");
                $(window).bind("scroll", function () {
                    base()
                })
            }
            $([window, document]).blur(function () {
                J = false
            }).focus(function () {
                if (J == false) {
                    l = 1
                }
                J = true
            });
            ajaxReceive();
        }
        function base() {
            $("#jfbcchat_base").css("top", $(window).scrollTop() + $(window).height() - 25);
            $("#jfbcchat_userstab_popup").css("top", parseInt($(window).height()) - parseInt($("#jfbcchat_userstab_popup").css("bottom")) - parseInt($("#jfbcchat_userstab_popup").height()) + $(window).scrollTop() + "px");
            $("#jfbcchat_optionsbutton_popup").css("top", parseInt($(window).height()) - parseInt($("#jfbcchat_optionsbutton_popup").css("bottom")) - parseInt($("#jfbcchat_optionsbutton_popup").height()) + $(window).scrollTop() + "px");
            if ($("#jfbcchat_tooltip").length > 0) {
                $("#jfbcchat_tooltip").css("top", parseInt($(window).height()) - parseInt($("#jfbcchat_tooltip").css("bottom")) - parseInt($("#jfbcchat_tooltip").height()) + $(window).scrollTop() + "px")
            }
            if (T != "") {
                $("#jfbcchat_user_" + T + "_popup").css("position", "absolute");
                $("#jfbcchat_user_" + T + "_popup").css("top", parseInt($(window).height()) - parseInt($("#jfbcchat_user_" + T + "_popup").css("bottom")) - parseInt($("#jfbcchat_user_" + T + "_popup").height()) + $(window).scrollTop() + "px")
            }
        }
        //Start dell'application
        startApp();
    }
} 

/*
 * jQuery.ScrollTo
 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * Date: 5/25/2009
 */

var scroller = function ($) {
    var a = $.scrollTo = function (f, e, d) {
        $(window).scrollTo(f, e, d)
    };
    $.defaults = {
        axis: "xy",
        duration: parseFloat($.fn.jquery) >= 1.3 ? 0 : 1
    };
    $.window = function (d) {
        return $(window)._scrollable()
    };
    $.fn._scrollable = function () {
        return this.map(function () {
            var e = this,
                d = !e.nodeName || $.inArray(e.nodeName.toLowerCase(), ["iframe", "#document", "html", "body"]) != -1;
            if (!d) {
                return e
            }
            var f = (e.contentWindow || e).document || e.ownerDocument || e;
            return $.browser.safari || f.compatMode == "BackCompat" ? f.body : f.documentElement
        })
    };
    $.fn.scrollTo = function (f, e, d) {
        if (typeof e == "object") {
            d = e;
            e = 0
        }
        if (typeof d == "function") {
            d = {
                onAfter: d
            }
        }
        if (f == "max") {
            f = 9000000000
        }
        d = $.extend({}, $.defaults, d);
        e = e || d.speed || d.duration;
        d.queue = d.queue && d.axis.length > 1;
        if (d.queue) {
            e /= 2
        }
        d.offset = b(d.offset);
        d.over = b(d.over);
        return this._scrollable().each(function () {
            var l = this,
                j = $(l),
                k = f,
                i, g = {},
                m = j.is("html,body");
            switch (typeof k) {
            case "number":
            case "string":
                if (/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(k)) {
                    k = b(k);
                    break
                }
                k = $(k, this);
            case "object":
                if (k.is || k.style) {
                    i = (k = $(k)).offset()
                }
            }
            $.each(d.axis.split(""), function (q, r) {
                var s = r == "x" ? "Left" : "Top",
                    u = s.toLowerCase(),
                    p = "scroll" + s,
                    o = l[p],
                    n = $.max(l, r);
                if (i) {
                    g[p] = i[u] + (m ? 0 : o - j.offset()[u]);
                    if (d.margin) {
                        g[p] -= parseInt(k.css("margin" + s)) || 0;
                        g[p] -= parseInt(k.css("border" + s + "Width")) || 0
                    }
                    g[p] += d.offset[u] || 0;
                    if (d.over[u]) {
                        g[p] += k[r == "x" ? "width" : "height"]() * d.over[u]
                    }
                } else {
                    var t = k[u];
                    g[p] = t.slice && t.slice(-1) == "%" ? parseFloat(t) / 100 * n : t
                }
                if (/^\d+$/.test(g[p])) {
                    g[p] = g[p] <= 0 ? 0 : Math.min(g[p], n)
                }
                if (!q && d.queue) {
                    if (o != g[p]) {
                        h(d.onAfterFirst)
                    }
                    delete g[p]
                }
            });
            h(d.onAfter);

            function h(n) {
                j.animate(g, e, d.easing, n &&
                function () {
                    n.call(this, f, d)
                })
            }
        }).end()
    };
    $.max = function (j, i) {
        var h = i == "x" ? "Width" : "Height",
            e = "scroll" + h;
        if (!$(j).is("html,body")) {
            return j[e] - $(j)[h.toLowerCase()]()
        }
        var g = "client" + h,
            f = j.ownerDocument.documentElement,
            d = j.ownerDocument.body;
        return Math.max(f[e], d[e]) - Math.min(f[g], d[g])
    };

    function b(d) {
        return typeof d == "object" ? d : {
            top: d,
            left: d
        }
    }
}

jQuery(document).ready(function ($) {
	var oTranslator = yTranslation();
	//Chat JQuery Plugin
	initialize($);
	//Scroller JQuery Plugin
	scroller($);
	//Adesso start dell'application
    $.jfbcchat();
    //Sound messageNotifier
    //msgNotify = new messagesNotifier('alert.mp3','clients.mp3'); 
    //msgNotify.registerEvents();
	
});
