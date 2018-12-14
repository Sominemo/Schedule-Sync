/* global actions, _, engines, app, xhr, popup, sxhr */
/* exported generateMD_method */

// Generator XXR
document.getElementById("menu-action").addEventListener("click", function (a) {
    let style0 = getComputedStyle(document.body);
    style0 = parseInt(style0.getPropertyValue("--header-height"));
    let style1 = getComputedStyle(document.getElementById("menu-action"));
    style1 = parseInt(style1.getPropertyValue("--icon-height"));
    let move0 = style0 * 0.65 / 2 - style1 * 0.5;
    let move1 = style0 * 0.65 / 2 - style1 * 0.30;
    actions.show(a.target.offsetLeft + move1 * 2, a.target.offsetTop + move0, a.target, [
        ["share", _("copy_link"), engines.copyLink],
        ["translate", _("lang"), chooserLang],
        (app.window_type == "method" ? ["format_indent_increase", _("gen_md"), engines.getMD] : []),
        ["select_all", _((app.copying ? "deny" : "allow") + "_selection"), function (){ engines.copyRule((app.copying ? 0 : 1)); }],
        ["feedback", feedBackTool.popupString(), feedBackTool.switch],
        ["info_outline", _("about"), aboutScreen]
    ]);
    a.preventDefault();
});

document.onscroll = function () {
    if (window.scrollY > 0) {
        document.documentElement.classList.add("--scrolled-body");
    } else {
        document.documentElement.classList.remove("--scrolled-body");
    }
};

function startLangLoad() {
    let t = localStorage.getItem("lang");
if (t) app.lang = t;
};

startLangLoad();

_.prototype.loadLang(windower);


let style01 = getComputedStyle(document.body);
style01 = style01.getPropertyValue("--main-color");
let m = document.createElement("meta");
m.setAttribute("name", "theme-color");
m.setAttribute("content", style01);
document.head.appendChild(m);

if (engines.CSSsupported("user-select", "none") || engines.CSSsupported("-moz-user-select", "none") || engines.CSSsupported("-webkit-user-select", "none") || engines.CSSsupported("-ms-user-select", "none") || engines.CSSsupported("-khtml-user-select", "none")) app.css_copy_lock_supp = true;
engines.copyRule(parseInt(localStorage.getItem("copying")));

document.body.onselectstart = function (e) {
    if (app.copying == true || app.css_copy_lock_supp == true) return true;
    if (e.target.nodeName != "INPUT" && e.target.nodeName != "TEXTAREA") {
        e.preventDefault();
        return false;
    }
    return true;
};

function getMain(al) {
    if (window.location.hash != "") window.location.hash = "";
    app.window_type = "main";
    engines.cleanPath();
    xhr("data/map/main.json", function () {

        xhr("data/map/" + app.lang + "/sections.json", function (b) {
            b = JSON.parse(b);
            engines.cleanMain();
            b.tabs.forEach(function (e) {
                let card = document.createElement("div");
                card.classList.add("card");

                let heading = document.createElement("div");
                heading.classList.add("head");
                heading.innerHTML = e.display;
                card.appendChild(heading);

                e.sections.sort(function (a, b) {
                    var nameA = a.display.toUpperCase();
                    var nameB = b.display.toUpperCase();
                    if (nameA < nameB) return -1;
                    if (nameA > nameB) return 1;
                    return 0;
                });

                e.sections.forEach(function (m) {
                    let l = document.createElement("div");
                    l.classList.add("item");
                    l.classList.add("clickable");
                    l.innerHTML = m.display;
                    l.onclick = function () {
                        window.location.hash = "#a-" + m.name;
                    };
                    card.appendChild(l);
                });
                document.getElementById("main").appendChild(card);
            });
            if (getUrlParameter("linking") !== "") feedBackTool.loadLink();
            if (typeof al === "function") al();
        });
    });
}

let xxhry = {};

function xxhr(a, c) {
    let g = Math.random();
    xxhry[g] = {};
    xxhry[g]["done"] = 0;
    xxhry[g]["results"] = [];
    a.forEach(function(e) {
        xhr(e, function (m, l) {
            xxhry[g]["done"]++;
            let ni = a.findIndex(function (er) {return er === l});
            xxhry[g]["results"][ni] = m;
            if (xxhry[g]["done"] === a.length) {
                c(g);
            }
        }, function (l) {
            xxhry[g]["done"]++;
            let ni = a.findIndex(function (er) {return er === l});
            xxhry[g]["results"][ni] = false;
            if (xxhry[g]["done"] === a.length) {
                c(g);
            }
        });
    });
}

var section = {
    open: function (a, ol) {
        a = a.toString();
        xhr("data/map/" + app.lang + "/sections.json", function (r) {
            r = JSON.parse(r);
            xhr("data/map/" + app.lang + "/methods.json", function (b) {
                b = JSON.parse(b);
                engines.cleanMain();
                engines.cleanPath();

                let card = document.createElement("div");
                card.classList.add("card");

                let heading = document.createElement("div");
                heading.classList.add("head");
                let mn;
                r.tabs.some(function(e) {
                    mn = e.sections.find(function(gl) {
                        return gl.name == a;
                    });
                    return mn != undefined;
                });
                if (mn === undefined) {
                    try {
                        if (history && history.length > 0) {
                            history.back();
                        } else {
                            location.hash = "";
                        }
                    } catch (e) {
                        location.hash = "";
                    }

                    let info_card = document.createElement("div");
                    info_card.classList.add("card");

                    let la_hint = document.createElement("div");
                    la_hint.classList.add("item");
                    la_hint.classList.add("flex-hint");

                    let icon_holder = document.createElement("div");
                    icon_holder.classList.add("icon_container");
                    la_hint.appendChild(icon_holder);

                    let icon = document.createElement("icon");
                    icon.innerText = "error";
                    icon_holder.appendChild(icon);

                    let text = document.createElement("div");
                    text.classList.add("hint-text");

                    text.innerHTML = _("404_text", {
                        "object": _("section"),
                        "name": a.charAt(0).toUpperCase() + a.slice(1)
                    });
                    la_hint.appendChild(text);

                    info_card.appendChild(la_hint);

                    popup.liteShow(info_card, _("404_error"));
                    return;

                }
                app.window_type = "section";
                heading.innerHTML = mn.display;
                card.appendChild(heading);
                if (b.stuff[a].about) {
                    let abouT = document.createElement("div");
                    abouT.classList.add("item");
                    abouT.innerHTML = b.stuff[a].about;
                    card.appendChild(abouT);
                    document.getElementById("main").appendChild(card);
                    card = document.createElement("div");
                    card.classList.add("card");
                }

                let fre = document.getElementById("home-nav-el");
                if (fre !== null) fre.parentElement.removeChild(fre);

                let sect = document.createElement("div");
                sect.classList.add("el");
                sect.id = "home-nav-el";
                sect.innerHTML = mn.display;
                document.getElementsByClassName("way-path")[0].appendChild(sect);

                let l;

                if (b.stuff[a]["methods"]) l = b.stuff[a]["methods"];
                else return;

                let ls = [];
                l.forEach(function(w) {
                    ls.push("data/methods/" + app.lang + "/" + w + ".json");
                });

                xxhr(ls, function (kk) {
                    xxhry[kk].results.forEach(function(q, i){ xxhry[kk].results[i] = JSON.parse(q)});
                    xxhry[kk].results.sort(function(a, b) {
                        if (a == false) return -1;
                        if (b == false) return 0;
                        var nameA = a.display.toUpperCase();
                        var nameB = b.display.toUpperCase();
                        if (nameA < nameB) return -1;
                        if (nameA > nameB) return 1;
                        return 0;
                    });
                    xxhry[kk].results.forEach(function(q) {
                        if (q != false) {
                            let method = document.createElement("div");
                            method.classList.add("item");
                            method.innerHTML = q.display + "<span class=\"light-color\"> — " + q.purpose + "</span>";
                            method.classList.add("clickable");
                            method.onclick = function() {
                                window.location.hash = "#b-" + q.name;
                            };

                            card.appendChild(method);
                        }
                    });
                    if (getUrlParameter("linking") !== "") feedBackTool.loadLink();
                    if (typeof ol === "function") ol();
                });
                document.getElementById("main").appendChild(card);
            });
        });
    }
};

function chooserLang() {
    let container = document.createElement("div");
    container.classList.add("multi-block-choose");

    app.langs.forEach(function(a) {
        let r = document.createElement("div");
        r.classList.add("no-select");
        r.innerText = a.toUpperCase();
        container.appendChild(r);
        r.addEventListener("click", function() {
            toggleLang(a);
            popup.hideAll();
        });
    });

    popup.liteShow(container, _("lang"));

}

function aboutScreen() {
    popup.show(_("about_screen"), _("about"));
}

function toggleLang(a, o) {
    app.lang = a;
    _.prototype.loadLang(function() {windower(o);});
}

function varType(a) {
    a = a.split(" ");

    switch (a[0]) {
    case "int":
        if (a[1] == "timestamp") return _("type__timestamp");
        else
            return _("type__int");

    case "string":
        return _("type__string");

    case "bool":
        return _("type__bool");

    case "binary":
        return _("type__binary");

    case "mixed":
        return _("type__mixed");

    case "comma":
        if (a[1] === "string") return _("type__strings") + ", " + _("type__comma_separated");
        if (a[1] === "int") return _("type__ints") + ", " + _("type__comma_separated");
        break;

    case "class":

        var re = "";

        var o = sxhr("data/methods/" + app.lang + "/class." + a[1] + ".json");
        var name;
        try {
            o = JSON.parse(o);
            name = o.display;
        } catch (e) {
            name = _("class") + " ???";
        }
        re = "<a href=\"#b-class." + a[1] + "\" target=\"_blank\">" + name + "</a>";
        return re;

    case "array":
        var rt = varType(a.slice(1).join(" "));
        return _("type__array") + ", " + _("that_contains") + " " + _("objects") + " " + _("of_type") + " " + rt;

    default:
        return "???";
    }
}

var patt = [];

var method = {
    open: function (a, ol) {
        xxhr(["data/map/" + app.lang + "/methods.json", "data/map/" + app.lang + "/sections.json", "data/methods/" + app.lang + "/" + a + ".json"], function (jk) {
            let l = xxhry[jk].results;

            let w = null;
            let d = null;

            l.forEach(function(m, i) {
                l[i] = JSON.parse(m);
            });

            let x = Object.keys(l[0].stuff);
            let y = Object.values(l[0].stuff);

            y.some(function(e, i) {
                if (e.methods) var s = e.methods.find(function(z) {
                    return z === a;
                });
                else s = undefined;

                if (s) w = x[i];
            });

            if (w !== null) {

                l[1].tabs.some(function(e) {
                    d = e.sections.find(function(gl) {
                        return gl.name === w;
                    });
                    return d != undefined;
                });
            }

            if (typeof l[2] !== "object") {
                try {
                    if (history && history.length > 0) {
                        history.back();
                    } else {
                        location.hash = "";
                    }
                } catch (e) {
                    location.hash = "";
                }

                let info_card = document.createElement("div");
                info_card.classList.add("card");

                let la_hint = document.createElement("div");
                la_hint.classList.add("item");
                la_hint.classList.add("flex-hint");

                let icon_holder = document.createElement("div");
                icon_holder.classList.add("icon_container");
                la_hint.appendChild(icon_holder);

                let icon = document.createElement("icon");
                icon.innerText = "error";
                icon_holder.appendChild(icon);

                let text = document.createElement("div");
                text.classList.add("hint-text");

                text.innerHTML = _("404_text", {
                    "object": _("method"),
                    "name": a.charAt(0).toUpperCase() + a.slice(1)
                });
                la_hint.appendChild(text);

                info_card.appendChild(la_hint);

                popup.liteShow(info_card, _("404_error"));
                return;

            }
            app.window_type = "method";
            engines.cleanMain();
            engines.loading();

            if (w === null) w = "???";
            if (d === null) d = w;
            else d = d.display;

            engines.cleanMain();
            engines.cleanPath();

            let fre = document.getElementById("home-nav-el");
            if (fre !== null) fre.parentElement.removeChild(fre);

            let sect = document.createElement("div");
            sect.classList.add("el");
            sect.id = "home-nav-el";
            sect.innerHTML = d;
            sect.onclick = function () {
                window.location.hash = "#a-" + w;
            };
            document.getElementsByClassName("way-path")[0].appendChild(sect);

            fre = document.getElementById("home-meth-el");
            if (fre !== null) fre.parentElement.removeChild(fre);

            sect = document.createElement("div");
            sect.classList.add("el");
            sect.id = "home-meth-el";
            sect.innerHTML = l[2].display;
            sect.onclick = function () {
                window.location.hash = "#b-" + l[2].name;
            };
            document.getElementsByClassName("way-path")[0].appendChild(sect);

            let card = document.createElement("div");
            card.classList.add("card");

            let head = document.createElement("div");
            head.classList.add("head");
            head.innerHTML = l[2].display;
            card.appendChild(head);

            let ch = false;
            let t = null;

            if (l[2].way && !l[2].purpose) {
                ch = true;
                t = l[2].way;
            } else if (l[2].purpose) {
                t = l[2].purpose;
            } else {
                t = null;
            }

            if (t !== null) {
                let tip = document.createElement("div");
                tip.classList.add("item");
                tip.innerHTML = t;
                card.appendChild(tip);
            }

            document.getElementById("main").appendChild(card);

            if (!ch && l[2].way) {
                card = document.createElement("div");
                card.classList.add("card");
                let tip = document.createElement("div");
                tip.classList.add("item");
                tip.innerHTML = l[2].way;
                card.appendChild(tip);
                document.getElementById("main").appendChild(card);
            }

            let e = l[2];
            app.window.method_json = e;
            e = JSON.parse(JSON.stringify(e));

            patt = [{
                "name": "token",
                "icon": "vpn_key",
                "text": _("token_att_text"),
                "related_link": "#b-token.get"
            },
            {
                "name": "user_fields",
                "icon": "people",
                "text": _("user_fields_att_text"),
                "related_link": "#b-global_vars.user_fields"
            },
            {
                "name": "pages_config",
                "icon": "find_in_page",
                "text": _("pages_config_att_text"),
                "related_link": "#b-global_vars.pages_config"
            }
            ];

            if (e.request) {
                card = document.createElement("div");
                card.classList.add("card");

                head = document.createElement("div");
                head.classList.add("head");
                head.innerHTML = _("request");
                card.appendChild(head);

                let p_a = [];

                if (e.request.length > 0) {

                    e.request.forEach(function(ty, i) {
                        let p = patt.findIndex(function(o) {
                            return o.name == ty.name;
                        });
                        if (p !== -1) p_a.push([p, i]);
                    });

                    if (p_a.length > 0) {
                        let h_card = document.createElement("div");
                        h_card.classList.add("card");
                        head = document.createElement("div");
                        head.classList.add("head");
                        head.innerHTML = _("hints");
                        h_card.appendChild(head);

                        p_a.forEach(function(rt) {
                            rt = patt[rt[0]];
                            let la_hint = document.createElement("div");
                            la_hint.classList.add("item");
                            la_hint.classList.add("flex-hint");
                            if (rt.related_link) {
                                la_hint.classList.add("clickable");
                                la_hint.onclick = function() {
                                    if (rt.related_link[0] == "#") location.hash = rt.related_link;
                                    else location.href = rt.related_link;
                                };
                            }

                            let icon_holder = document.createElement("div");
                            icon_holder.classList.add("icon_container");
                            la_hint.appendChild(icon_holder);

                            let icon = document.createElement("icon");
                            icon.innerText = rt.icon;
                            icon_holder.appendChild(icon);

                            let text = document.createElement("div");
                            text.classList.add("hint-text");
                            text.innerHTML = rt.text;
                            la_hint.appendChild(text);

                            h_card.appendChild(la_hint);


                            e.request.splice(rt[1], 1);
                        });
                        document.getElementById("main").appendChild(h_card);
                    }

                    let table = document.createElement("table");
                    table.classList.add("fields-table");
                    e.request.forEach(function (z) {
                        let row = document.createElement("tr");

                        let name_f = document.createElement("td");
                        name_f.innerHTML = z.name;
                        row.appendChild(name_f);

                        let info = document.createElement("td");

                        let infy = document.createElement("div");
                        infy.innerHTML = z.info;

                        let type = document.createElement("div");
                        type.innerHTML = varType(z.type) + (z.important ? ", <b>" + _("required_field") + "</b>" : "");

                        info.appendChild(infy);
                        info.appendChild(type);

                        row.appendChild(info);
                        table.appendChild(row);
                    });
                    card.appendChild(table);

                } else {
                    let item = document.createElement("div");
                    item.classList.add("item");
                    item.innerHTML = "<span class=\"light-color\">" + _("no_data") + "</span>";
                    card.appendChild(item);
                }
                if (e.request.length > 0) document.getElementById("main").appendChild(card);
            }

            if (e.answer) {
                card = document.createElement("div");
                card.classList.add("card");

                head = document.createElement("div");
                head.classList.add("head");
                head.innerHTML = _("response");
                card.appendChild(head);

                if (e.answer.length > 0) {

                    let table = document.createElement("table");
                    table.classList.add("fields-table");
                    e.answer.forEach(function(z) {
                        let row = document.createElement("tr");

                        let name_f = document.createElement("td");
                        name_f.innerHTML = z.name;
                        row.appendChild(name_f);

                        let info = document.createElement("td");

                        let infy = document.createElement("div");
                        infy.innerHTML = z.info;

                        let type = document.createElement("div");
                        type.innerHTML = varType(z.type) + (z.important ? ", <b>" + _("required_field") + "</b>" : "");

                        info.appendChild(infy);
                        info.appendChild(type);

                        row.appendChild(info);
                        table.appendChild(row);
                    });
                    card.appendChild(table);

                } else {
                    let item = document.createElement("div");
                    item.classList.add("item");
                    item.innerHTML = "<span class=\"light-color\">" + _("no_data") + "</span>";
                    card.appendChild(item);
                }
                document.getElementById("main").appendChild(card);
            }

            if (getUrlParameter("linking") !== "") feedBackTool.loadLink();
            if (typeof ol === "function") ol();
        });
    }
};

function html2MD(s) {
    s = s.replace(/<br>/gi, "  \n")
        .replace(/href="(#[^\s]+)"/gi, "href=\"" + app.link + "$1\"")
        .replace(/<a href="([^\s]+)"(.+)?>([^<>]+)<\/a>/gi, "[$3]($1)")
        .replace(/<b>([^<>]+)<\/b>/gi, "**$1**")
        .replace(/<code>([^<>]+)<\/code>/gi, "`$1`")
        .replace(/<\/?ul>/gi, "")
        .replace(/<li>([^<>]+)<\/li>/gi, "  \n* $1\n  ");
    return s;
}

function generateMD_method() {
    let a = JSON.parse(JSON.stringify(app.window.method_json));
    let text = "";
    // Title
    text += "# " + a.display + (a.display !== a.name ? "  \n(" + a.name + ")" : "") + "  \n";

    if (a.purpose) text += "_" + html2MD(a.purpose) + "_  \n";
    if (a.way) text += html2MD(a.way) + "\n";

    if (a.request) {

        let p_a = [];

        if (a.request.length > 0) {

            a.request.forEach(function(ty, i) {
                let p = patt.findIndex(function (o) {
                    return o.name == ty.name;
                });
                if (p !== -1) p_a.push([p, i]);
            });

            if (p_a.length > 0) {
                text += "## " + _("hints") + "  \n";

                p_a.forEach(function(rt) {
                    rt = patt[rt[0]];
                    text += "* " + (rt.related_link ? "[" : "") + html2MD(rt.text) + (rt.related_link ? "](" + (rt.related_link[0] == "#" ? app.link + rt.related_link : rt.related_link) + ")" : "") + "  \n";
                    a.request.splice(rt[1], 1);
                });
            }

            if (a.request.length > 0) text += "## " + _("request") + "  \n";
            a.request.forEach(function(z) {
                text += "* **" + z.name + "**  \n";
                text += html2MD(z.info) + "  \n";
                text += "_" + html2MD(varType(z.type)) + (z.important ? ", **" + _("required_field") + "**" : "") + "_  \r\n";
            });

        } else {
            text += "_" + _("no_data") + "_  \r\n";
        }
    }

    if (a.answer) {

        if (a.answer.length > 0) {
            text += "## " + _("response") + "  \n";
            a.answer.forEach(function(z) {
                text += "* **" + z.name + "**  \n";
                text += html2MD(z.info) + "  \n";
                text += "_" + html2MD(varType(z.type)) + (z.important ? ", **" + _("required_field") + "**" : "") + "_  \r\n";
            });

        } else {
            text += "_" + _("no_data") + "_  \r\n";
        }
    }


    return text;
}

function windower(o) {
    let hash = window.location.hash;
    var linkReg;
    if ((linkReg = hash.match(/#a-([a-zA-Z0-9_-]+)/i)) !== null) {
        section.open(linkReg[1], o);
    } else if ((linkReg = hash.match(/#b-([a-zA-Z0-9.a-zA-Z0-9_-]+)/i)) !== null) {
        method.open(linkReg[1], o);
    } else if (window.location.hash == "") {
        getMain(o);
    } else window.location.hash = "";
}

function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
};

window.onhashchange = windower;

!function(e, t){e.findAndReplaceDOMText=function(){var e="retain",t="first",n=document,r={}.hasOwnProperty;function i(){return function(e,t,n,r,d){if(t&&!t.nodeType&&arguments.length<=2)return!1;var a="function"==typeof n;a&&(s=n,n=function(e,t){return s(e.text,t.startIndex)});var s;var p=o(t,{find:e,wrap:a?null:n,replace:a?n:"$"+(r||"&"),prepMatch:function(e,t){if(!e[0])throw"findAndReplaceDOMText cannot handle zero-length matches";if(r>0){var n=e[r];e.index+=e[0].indexOf(n),e[0]=n}return e.endIndex=e.index+e[0].length,e.startIndex=e.index,e.index=t,e},filterElements:d});return i.revert=function(){return p.revert()},!0}.apply(null,arguments)||o.apply(null,arguments)}function o(e,t){return new d(e,t)}function d(t,n){var o=n.preset&&i.PRESETS[n.preset];if(n.portionMode=n.portionMode||e,o)for(var d in o)r.call(o,d)&&!r.call(n,d)&&(n[d]=o[d]);this.node=t,this.options=n,this.prepMatch=n.prepMatch||this.prepMatch,this.reverts=[],this.matches=this.search(),this.matches.length&&this.processMatches()}return i.NON_PROSE_ELEMENTS={br:1,hr:1,script:1,style:1,img:1,video:1,audio:1,canvas:1,svg:1,map:1,object:1,input:1,textarea:1,select:1,option:1,optgroup:1,button:1},i.NON_CONTIGUOUS_PROSE_ELEMENTS={address:1,article:1,aside:1,blockquote:1,dd:1,div:1,dl:1,fieldset:1,figcaption:1,figure:1,footer:1,form:1,h1:1,h2:1,h3:1,h4:1,h5:1,h6:1,header:1,hgroup:1,hr:1,main:1,nav:1,noscript:1,ol:1,output:1,p:1,pre:1,section:1,ul:1,br:1,li:1,summary:1,dt:1,details:1,rp:1,rt:1,rtc:1,script:1,style:1,img:1,video:1,audio:1,canvas:1,svg:1,map:1,object:1,input:1,textarea:1,select:1,option:1,optgroup:1,button:1,table:1,tbody:1,thead:1,th:1,tr:1,td:1,caption:1,col:1,tfoot:1,colgroup:1},i.NON_INLINE_PROSE=function(e){return r.call(i.NON_CONTIGUOUS_PROSE_ELEMENTS,e.nodeName.toLowerCase())},i.PRESETS={prose:{forceContext:i.NON_INLINE_PROSE,filterElements:function(e){return!r.call(i.NON_PROSE_ELEMENTS,e.nodeName.toLowerCase())}}},i.Finder=d,d.prototype={search:function(){var e,t=0,n=0,r=this.options.find,i=this.getAggregateText(),o=[],d=this;return r="string"==typeof r?RegExp(String(r).replace(/([.*+?^=!:${}()|[\]\/\\])/g,"\\$1"),"g"):r,function i(a){for(var s=0,p=a.length;s<p;++s){var h=a[s];if("string"==typeof h){if(r.global)for(;e=r.exec(h);)o.push(d.prepMatch(e,t++,n));else(e=h.match(r))&&o.push(d.prepMatch(e,0,n));n+=h.length}else i(h)}}(i),o},prepMatch:function(e,t,n){if(!e[0])throw new Error("findAndReplaceDOMText cannot handle zero-length matches");return e.endIndex=n+e.index+e[0].length,e.startIndex=n+e.index,e.index=t,e},getAggregateText:function(){var e=this.options.filterElements,t=this.options.forceContext;return function n(r){if(r.nodeType===Node.TEXT_NODE)return[r.data];if(e&&!e(r))return[];var i=[""],o=0;if(r=r.firstChild)do{if(r.nodeType!==Node.TEXT_NODE){var d=n(r);t&&r.nodeType===Node.ELEMENT_NODE&&(!0===t||t(r))?(i[++o]=d,i[++o]=""):("string"==typeof d[0]&&(i[o]+=d.shift()),d.length&&(i[++o]=d,i[++o]=""))}else i[o]+=r.data}while(r=r.nextSibling);return i}(this.node)},processMatches:function(){var e,t,n,r=this.matches,i=this.node,o=this.options.filterElements,d=[],a=i,s=r.shift(),p=0,h=0,l=[i];e:for(;;){if(a.nodeType===Node.TEXT_NODE&&(!t&&a.length+p>=s.endIndex?t={node:a,index:h++,text:a.data.substring(s.startIndex-p,s.endIndex-p),indexInMatch:0===p?0:p-s.startIndex,indexInNode:s.startIndex-p,endIndexInNode:s.endIndex-p,isEnd:!0}:e&&d.push({node:a,index:h++,text:a.data,indexInMatch:p-s.startIndex,indexInNode:0}),!e&&a.length+p>s.startIndex&&(e={node:a,index:h++,indexInMatch:0,indexInNode:s.startIndex-p,endIndexInNode:s.endIndex-p,text:a.data.substring(s.startIndex-p,s.endIndex-p)}),p+=a.data.length),n=a.nodeType===Node.ELEMENT_NODE&&o&&!o(a),e&&t){if(a=this.replaceMatch(s,e,d,t),p-=t.node.data.length-t.endIndexInNode,e=null,t=null,d=[],h=0,!(s=r.shift()))break}else if(!n&&(a.firstChild||a.nextSibling)){a.firstChild?(l.push(a),a=a.firstChild):a=a.nextSibling;continue}for(;;){if(a.nextSibling){a=a.nextSibling;break}if((a=l.pop())===i)break e}}},revert:function(){for(var e=this.reverts.length;e--;)this.reverts[e]();this.reverts=[]},prepareReplacementString:function(e,n,r){var i=this.options.portionMode;return i===t&&n.indexInMatch>0?"":(e=e.replace(/\$(\d+|&|`|')/g,function(e,t){var n;switch(t){case"&":n=r[0];break;case"`":n=r.input.substring(0,r.startIndex);break;case"'":n=r.input.substring(r.endIndex);break;default:n=r[+t]||""}return n}),i===t?e:n.isEnd?e.substring(n.indexInMatch):e.substring(n.indexInMatch,n.indexInMatch+n.text.length))},getPortionReplacementNode:function(e,t){var r=this.options.replace||"$&",i=this.options.wrap,o=this.options.wrapClass;if(i&&i.nodeType){var d=n.createElement("div");d.innerHTML=i.outerHTML||(new XMLSerializer).serializeToString(i),i=d.firstChild}if("function"==typeof r)return(r=r(e,t))&&r.nodeType?r:n.createTextNode(String(r));var a="string"==typeof i?n.createElement(i):i;return a&&o&&(a.className=o),(r=n.createTextNode(this.prepareReplacementString(r,e,t))).data&&a?(a.appendChild(r),a):r},replaceMatch:function(e,t,r,i){var o,d,a=t.node,s=i.node;if(a===s){var p=a;t.indexInNode>0&&(o=n.createTextNode(p.data.substring(0,t.indexInNode)),p.parentNode.insertBefore(o,p));var h=this.getPortionReplacementNode(i,e);return p.parentNode.insertBefore(h,p),i.endIndexInNode<p.length&&(d=n.createTextNode(p.data.substring(i.endIndexInNode)),p.parentNode.insertBefore(d,p)),p.parentNode.removeChild(p),this.reverts.push(function(){o===h.previousSibling&&o.parentNode.removeChild(o),d===h.nextSibling&&d.parentNode.removeChild(d),h.parentNode.replaceChild(p,h)}),h}o=n.createTextNode(a.data.substring(0,t.indexInNode)),d=n.createTextNode(s.data.substring(i.endIndexInNode));for(var l=this.getPortionReplacementNode(t,e),c=[],u=0,f=r.length;u<f;++u){var N=r[u],x=this.getPortionReplacementNode(N,e);N.node.parentNode.replaceChild(x,N.node),this.reverts.push(function(e,t){return function(){t.parentNode.replaceChild(e.node,t)}}(N,x)),c.push(x)}var g=this.getPortionReplacementNode(i,e);return a.parentNode.insertBefore(o,a),a.parentNode.insertBefore(l,a),a.parentNode.removeChild(a),s.parentNode.insertBefore(g,s),s.parentNode.insertBefore(d,s),s.parentNode.removeChild(s),this.reverts.push(function(){o.parentNode.removeChild(o),l.parentNode.replaceChild(a,l),d.parentNode.removeChild(d),g.parentNode.replaceChild(s,g)}),g}},i}()}(this);

var snack = {
    textBuffer: [],
    displayed: 0,
    show: function(text) {
        if (snack.displayed && text !== undefined) {
            snack.textBuffer.push(text);
        } else {
            if (text !== undefined) snack.textBuffer.push(text);
            if (snack.textBuffer.length > 0) {
                document.getElementById('snackbar-text').innerHTML = snack.textBuffer[0];
                document.getElementById('snackbar').classList.add('active');
                snack.displayed = 1;
                snackCloseTimer = setTimeout(function() {
                    snack.close();
                }, 5000);
            }
        }
    },
    close: function() {
        let a = document.querySelector('.active#snackbar');
        if (a) {
            a.classList.remove('active');
        setTimeout(function() {
            clearTimeout(snackCloseTimer);
            snack.textBuffer.splice(0, 1);
            document.getElementById('snackbar-text').innerHTML = '';
            snack.displayed = 0;
            snack.show();
        }, 100);
    }
    }
}

window.addEventListener("load", function() {
    document.getElementById("snackbar-close").onclick = snack.close;
})


let feedBackTool = {
    reciever_url: "https://procsec.top/prop/docHub/ComposeFeedBack",
    loader_url: "https://procsec.top/prop/docHub/LoadFeedBack",
    popupString: function () {
        if (app.feedback_mode) return _("disable_feedback_sending");
        return _("enable_feedback_sending");
    },

    status: function (set) {
        if (set === undefined) { var a = localStorage.getItem("feedback_mode_beta"); return a === "1" ? true : [(a === null ? true : false)]; }

        if (set) {
            localStorage.setItem("feedback_mode_beta", "1");
            app.feedback_mode = 1;
        } else {
            localStorage.setItem("feedback_mode_beta", "0");
            app.feedback_mode = 0;
        }
    },

    updateStatus: function () {
        (feedBackTool.status() === true ? feedBackTool.status(1) : null);
    },

    switch: function () {
        let b = feedBackTool.status();
        if (b === true) { return feedBackTool.status(0); }

        if (b[0]) feedBackTool.welcomeWindow();
        feedBackTool.status(1);
        engines.copyRule(1);
    },

    welcomeWindow: function () {
        popup.show(_("welcome_to_feedback_mode"), _("feedback_mode"));
    },

    selectionEndTimeout: false,

    selectReactor: function(a) {
        if (feedBackTool.status() !== true) return;
        if (feedBackTool.selectionEndTimeout) {
            clearTimeout(feedBackTool.selectionEndTimeout);
        }
    
        feedBackTool.selectionEndTimeout = setTimeout(function () {
            feedBackTool.selectFinishReactor();
        }, 500);
    },

    selectFinishReactor: function () {
        feedBackTool.callPopup();
    },

    callPopup: function() {
        try {
            b = getSelection();
            app.window.report_data_s = {};
            app.window.report_data_s.selection = b;
        } catch (e) {
            return;
        }
        if (b.type !== "Range") return;

        let v = b.getRangeAt(0).getClientRects();
        let i = feedBackTool.selDir(b);
        if (i) v = v[v.length-1];
        else v = v[0];

        actions.show(v.x, v.y, b.baseNode, [
            ["flag", _("compose_feedback"), feedBackTool.compose],
            ["content_copy", _("copy"), function(){engines.copy(false)}]
        ], "transform: translateY(50%)")
    },

    selDir: function(b) {
        var sel = b,
            position = sel.anchorNode.compareDocumentPosition(sel.focusNode),
            backward = false;
            if (!position && sel.anchorOffset > sel.focusOffset || 
            position === Node.DOCUMENT_POSITION_PRECEDING)
            backward = true;      
            return !backward;
        },

    compose: function() {
        if (app.window.report_data_m) {
            popup.show(_("reload_page_without_feedback_viewer_firstly"), _("error"), {buttons:'<div class="actionButton" onclick="feedBackTool.reload()">'+_("to_reload")+'</div>'});
            return;
        }
        feedBackTool.dataForFB();
        getSelection().removeAllRanges();

        let info_card = document.createElement("div");
        info_card.classList.add("card");

        let la_hint = document.createElement("div");
        la_hint.classList.add("item");
        la_hint.classList.add("flex-hint");

        let icon_holder = document.createElement("div");
        icon_holder.classList.add("icon_container");
        la_hint.appendChild(icon_holder);

        let icon = document.createElement("icon");
        icon.innerText = "feedback";
        icon_holder.appendChild(icon);

        let text = document.createElement("div");
        text.classList.add("hint-text");

        text.innerHTML = _("feedback_selection_captured");
        la_hint.appendChild(text);

        info_card.appendChild(la_hint);

        let cb = document.createElement("p");
        cb.innerHTML = _("tell_more_feedback") + ":";
        info_card.appendChild(cb);

        let cod = document.createElement("textarea");
        cod.classList.add("big_md_ta");
        cod.placeholder = _("feedback_details_are_going_there");
        cod.id = "feedback_details_box_ui";
        app.window.fb_description = cod;
        //cod.classList.add("grey");
        //cod.innerText = JSON.stringify(app.window.report_data.selection);

        info_card.appendChild(cod);

        let snd = document.createElement("button");
        snd.innerText = _("send_feedback");
        snd.classList.add("md_btn");
        snd.onclick = feedBackTool.send;

        info_card.appendChild(snd);

        popup.liteShow(info_card, _("compose_feedback"));
    },

    dataForFB: function() {
        let r = feedBackTool.selection.save(app.window.report_data_s.selection);
        app.window.report_data = {start: r.start, end: r.end, content: r.content, html: document.getElementsByTagName("main")[0].innerHTML};
    },

    dataToB64: function(d, text) {
        return encodeURIComponent(engines.b64EncodeUnicode(JSON.stringify([d[0], d[1], app.lang, text.toString()])));
    },

    showFound: function(b, c) {
        a = feedBackTool.wrapSelectedText([c, b.content]);
        getSelection().removeAllRanges();
        try {
        window.scrollTo(0, a[0][0].getBoundingClientRect().top - (parseInt(getComputedStyle(document.body).getPropertyValue("--header-height"))+10)); 
        } catch (e) {
        }
        try {
            [].forEach.call(a[0], function(e){
                e.classList.add("highlighted");
        });
            
        } catch (e) {
            popup.show(_("we_lost_some_parts_of_selection"), _("selection_cant_be_reproduced"));
        }
    },

    loadLink: function() {
        if (app.fb_ch) return;
        app.fb_ch = 1;
        if (feedBackTool.status() === true) feedBackTool.status(0);
        try {
        let a = getUrlParameter("linking");
            a = xhr(feedBackTool.loader_url+"?key="+a, function(a) {
            a = JSON.parse(a);
            if (a.hasOwnProperty("error")) {
                snack.show(_("cant_load_such_feedback_key"));
                return;
            }
            app.window.report_data_m = a;
            feedBackTool.putInHTML(a.html);
            snack.show(_("feedback_data_loaded"));
            let r = feedBackTool.selection.restore(a);
            feedBackTool.showFound(a, r.anchorNode);        
            });
        } catch (e) {
            console.log("FeedBack fetching failed");
        }
    },

    selection: {
        restore: function(state) {
            referenceNode = document.getElementsByTagName("main")[0];
            var i
              , node
              , nextNodeCharIndex
              , currentNodeCharIndex = 0
              , nodes = [referenceNode]
              , sel = window.getSelection()
              , range = document.createRange()
            range.setStart(referenceNode, 0)
            range.collapse(true)
            while (node = nodes.pop()) {
              if (node.nodeType === 3) { 
                nextNodeCharIndex = currentNodeCharIndex + node.length
                if (state.start >= currentNodeCharIndex && state.start <= nextNodeCharIndex) {
                  range.setStart(node, state.start - currentNodeCharIndex)
                }
                if (state.end >= currentNodeCharIndex && state.end <= nextNodeCharIndex) {
                  range.setEnd(node, state.end - currentNodeCharIndex)
                  break
                }
                currentNodeCharIndex = nextNodeCharIndex
              } else {
                i = node.childNodes.length
                while (i--) {
                  nodes.push(node.childNodes[i])
                }
              }
            }
            sel.removeAllRanges()
            sel.addRange(range)
            return sel
          },

          save: function(selection) {
            referenceNode =  document.getElementsByTagName("main")[0];
            var sel = selection || window.getSelection()
              , range = sel.rangeCount
                  ? sel.getRangeAt(0).cloneRange()
                  : document.createRange()
              , startContainer = range.startContainer
              , startOffset = range.startOffset
              , state = { content: range.toString() }
            range.selectNodeContents(referenceNode)
            range.setEnd(startContainer, startOffset)
            state.start = range.toString().length
            state.end = state.start + state.content.length
            return state
          }
    },

    putInHTML: function(html) {
        document.getElementsByTagName("main")[0].innerHTML = html;
    },

    wrapSelectedText: function(a) {       
        feedBackTool.highlight();
        feedBackTool.hls[0].classList.add("begin");
        feedBackTool.hls[feedBackTool.hls.length-1].classList.add("end");
       return [feedBackTool.hls, []];
    },

    highlight: function() {
        feedBackTool.hls = [];
        var range = window.getSelection().getRangeAt(0),
            parent = range.commonAncestorContainer,
            start = range.startContainer,
            end = range.endContainer;
        var startDOM = (start.parentElement == parent) ? start.nextSibling : start.parentElement;
        var currentDOM = startDOM.nextElementSibling;
        var endDOM = (end.parentElement == parent) ? end : end.parentElement;
        //Process Start Element
        feedBackTool.highlightText(startDOM, 'START', range.startOffset);
        while (currentDOM != endDOM && currentDOM != null) {
            feedBackTool.highlightText(currentDOM);
            currentDOM = currentDOM.nextElementSibling;
        }
        //Process End Element
        feedBackTool.highlightText(endDOM, 'END', range.endOffset);
    },
    
    highlightText: function(elem, offsetType, idx) {
        if (elem.nodeType == 3) {
            var span = document.createElement('span');
            span.setAttribute('class', 'marker-effect');
            span.onclick = function() {if (app.window.report_data_m.text) popup.show(app.window.report_data_m.text, _("info"))}
            feedBackTool.hls.push(span);
            var origText = elem.textContent, text, prevText, nextText;
            if (offsetType == 'START') {
                text = origText.substring(idx);
                prevText = origText.substring(0, idx);
            } else if (offsetType == 'END') {
                text = origText.substring(0, idx);
                nextText = origText.substring(idx);
            } else {
                text = origText;
            }
            span.textContent = text;
    
            var parent = elem.parentElement;
            parent.replaceChild(span, elem);
            if (prevText) { 
                var prevDOM = document.createTextNode(prevText);
                parent.insertBefore(prevDOM, span);
            }
            if (nextText) {
                var nextDOM = document.createTextNode(nextText);
                parent.appendChild(nextDOM);
            }
            return;
        }
        var childCount = elem.childNodes.length;
        for (var i = 0; i < childCount; i++) {
            if (offsetType == 'START' && i == 0) 
            feedBackTool.highlightText(elem.childNodes[i], 'START', idx);
            else if (offsetType == 'END' && i == childCount - 1)
            feedBackTool.highlightText(elem.childNodes[i], 'END', idx);
            else
            feedBackTool.highlightText(elem.childNodes[i]);
        }
    },

    searchers: {
        replaceLookup: function(text, interation) {
            feedBackTool.status(0);
            let r = findAndReplaceDOMText(document.getElementsByTagName("main")[0], {
                find: text,
                wrap: 'span',
                wrapClass: 'search-result'
            });
            let b = [];
            interation.forEach(function(e) {
                b.push(document.querySelectorAll("span.search-result")[e]);
            })
            return [b, r];
        },
    
        findReportSelection: function(s) {
          let p = s.getRangeAt(0).commonAncestorContainer;
          if (p.nodeType == 3) {
            p = p.parentNode;
          }
          let b_text = s.toString();
          let text = b_text.replace(/\r?\n|\r/g, "").replace(/\t/g, '');
          let r = findAndReplaceDOMText(document.getElementsByTagName("main")[0], {
            find: text,
            wrap: 'span',
            wrapClass: 'report-search-result'
        });
        app.window.report_revert = r;
        let l = document.querySelectorAll("span.report-search-result");
        let e = [];
            for (var i = 0; i < l.length; i++) {
                if (engines.isDescendant(p, l[i])) e.push(i);
            }
            return [text, e, b_text];
        }
    },

    send: function() {
        let d = feedBackTool.collectData();
        if (d === false) return;
        popup.hideAll();
        snack.close();
        snack.show(_("collecting_feedback_and_sending"));
        let s = new FormData;
        
        let dj = JSON.stringify(d);

        s.append("data", dj);
        setTimeout(() => {
            xhr(feedBackTool.reciever_url, function(a) {
                try {
                a = JSON.parse(a);
                } catch (e) {
                    snack.close();
                    snack.show(_("failed_contact_the_server"));
                    return;
                }
                if (a.hasOwnProperty("error")) {
                    snack.close();
                    snack.show(_("feedback_declined_with_error", {error: a.error}));
                    return;
                }
                snack.close();
                snack.show(_("done_thank_you_for_sending_feedback"));
            }, function() {
                snack.close();
                snack.show(_("failed_contact_the_server"));
            }, s);            
        }, 500);
    },

    collectData: function() {
      app.window.report_data.text = document.querySelector("#feedback_details_box_ui").value;
      if (app.window.report_data.text.trim() === "") {snack.show(_("feedback_details_must_be_filled")); return false;}
      app.window.report_data.url = location.href;
      return app.window.report_data;
      
    },

    showText: function() {
        if (!app.window.report_data[3]) return
        popup.show(app.window.report_data[3], _("info"));
    },

    reload: function() {
        let a = location.search.replace(/linking=[^&]+&?/, "");    
        if (a === "?") location.href = location.href.replace(/\?linking=[^&#]+&?/, ""); else
        location.search = a;
    },
    absorbEvent_: function(event) {
        if (feedBackTool.status() !== true) return;
        var e = event || window.event;
        e.preventDefault && e.preventDefault();
        e.stopPropagation && e.stopPropagation();
        e.cancelBubble = true;
        e.returnValue = false;
        return false;
      },
  
      preventLongPressMenu: function(node) {
        node.ontouchstart = absorbEvent_;
        node.ontouchmove = absorbEvent_;
        node.ontouchend = absorbEvent_;
        node.ontouchcancel = absorbEvent_;
      },
      resetAbsorb: function(node) {
        node.ontouchstart = null;
        node.ontouchmove = null;
        node.ontouchend =  null;
        node.ontouchcancel = null;
      }
};
feedBackTool.updateStatus();
/* document.addEventListener("touchend", function(a) {
    app.mouseTracker = {x: a.changedTouches[0].pageX, y: a.changedTouches[0].pageY};
});
document.addEventListener("mouseup", function(a) {
    app.mouseTracker = {x: a.pageX, y: a.pageY};
}); */
document.addEventListener("selectionchange", feedBackTool.selectReactor);

document.oncontextmenu = function(a) {
    if (feedBackTool.status() === true && engines.isDescendant(document.getElementsByTagName("main")[0], a.target)) return false;
}