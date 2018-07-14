// Generator XXR
document.getElementById("menu-action").addEventListener("click", function(a) {
    let style0 = getComputedStyle(document.body);
    style0 = parseInt(style0.getPropertyValue('--header-height'));
    let style1 = getComputedStyle(document.getElementById("menu-action"));
    style1 = parseInt(style1.getPropertyValue('--icon-height'));
    let move0 = style0 * 0.65 / 2 - style1 * 0.5;
    let move1 = style0 * 0.65 / 2 - style1 * 0.30;
    actions.show(a.target.offsetLeft + move1 * 2, a.target.offsetTop + move0, a.target, [
        ["share", _('copy_link'), engines.copyLink],
        ["translate", _('lang'), chooserLang],
        (app.window_type == "method" ? ["format_indent_increase", _('gen_md'), engines.getMD]: []),
        ["select_all", _((app.copying ? 'deny' : 'allow') + '_selection'), () => {engines.copyRule((app.copying ? 0 : 1))}],
        ["info_outline", _('about'), aboutScreen]
    ]);
    a.preventDefault();
});

document.onscroll = function(a) {
    if (window.scrollY > 0) {
        document.documentElement.classList.add("--scrolled-body");
    } else {
        document.documentElement.classList.remove("--scrolled-body");
    }
}

t = localStorage.getItem("lang");
if (t) app.lang = t;
_.prototype.loadLang(windower);


let style01 = getComputedStyle(document.body);
style01 = style01.getPropertyValue('--main-color');
let m = document.createElement("meta");
m.setAttribute("name", "theme-color");
m.setAttribute("content", style01);
document.head.appendChild(m);

if (engines.CSSsupported("user-select","none") || engines.CSSsupported("-moz-user-select","none") || engines.CSSsupported("-webkit-user-select","none") || engines.CSSsupported("-ms-user-select","none") || engines.CSSsupported("-khtml-user-select","none")) app.css_copy_lock_supp = true;
engines.copyRule(localStorage.getItem("copying"));

document.body.onselectstart = function(e) {
    if (app.copying == true || app.css_copy_lock_supp == true) return true;
    if (e.target.nodeName != "INPUT" && e.target.nodeName != "TEXTAREA") {
        e.preventDefault();
        return false;
    }
    return true;
}

function getMain() {
    if (window.location.hash != "") window.location.hash = "";
    app.window_type = "main";
    engines.cleanPath();
    xhr('data/map/main.json', function(a) {
        a = JSON.parse(a);

        xhr('data/map/' + app.lang + '/sections.json', function(b) {
            b = JSON.parse(b);
            engines.cleanMain();
            b.tabs.forEach(e => {
                card = document.createElement("div");
                card.classList.add("card");

                heading = document.createElement("div");
                heading.classList.add("head");
                heading.innerHTML = e.display;
                card.appendChild(heading);

                e.sections.sort((a, b) => {
                    var nameA = a.display.toUpperCase();
                    var nameB = b.display.toUpperCase();
                    if (nameA < nameB) return -1;
                    if (nameA > nameB) return 1;
                    return 0;
                });

                e.sections.forEach(m => {
                    l = document.createElement("div");
                    l.classList.add("item");
                    l.classList.add("clickable");
                    l.innerHTML = m.display;
                    l.onclick = () => {
                        window.location.hash = "#a-" + m.name
                    };
                    card.appendChild(l);
                });
                document.getElementById("main").appendChild(card);
            });
        });
    });
}

xxhry = {};

function xxhr(a, c) {
    g = Math.random();
    xxhry[g] = {};
    xxhry[g]["done"] = 0;
    xxhry[g]["results"] = [];
    a.forEach(e => {
        xhr(e, function(m, l) {
            xxhry[g]["done"]++;
            ni = a.findIndex((er) => er === l);
            xxhry[g]["results"][ni] = m;
            if (xxhry[g]["done"] === a.length) {
                c(g);
            }
        }, function(l) {
            xxhry[g]["done"]++;
            ni = a.findIndex((er) => er === l);
            xxhry[g]["results"][ni] = false;
            if (xxhry[g]["done"] === a.length) {
                c(g);
            }
        })
    });
}

var section = {
    open: function(a) {
        a = a.toString();
        xhr('data/map/' + app.lang + '/sections.json', function(r) {
            r = JSON.parse(r);
            xhr('data/map/' + app.lang + '/methods.json', function(b) {
                b = JSON.parse(b);
                engines.cleanMain();
                engines.cleanPath();

                let card = document.createElement("div");
                card.classList.add("card");

                let heading = document.createElement("div");
                heading.classList.add("head");
                r.tabs.some(e => {
                    mn = e.sections.find(gl => {
                        return gl.name == a
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

                    la_hint = document.createElement("div");
                    la_hint.classList.add("item");
                    la_hint.classList.add("flex-hint");

                    icon_holder = document.createElement("div");
                    icon_holder.classList.add("icon_container");
                    la_hint.appendChild(icon_holder);

                    let icon = document.createElement("icon");
                    icon.innerText = "error";
                    icon_holder.appendChild(icon);

                    let text = document.createElement("div");
                    text.classList.add("hint-text");

                    text.innerHTML = _('404_text', {
                        "object": _("section"),
                        "name": a.charAt(0).toUpperCase() + a.slice(1)
                    });
                    la_hint.appendChild(text);

                    info_card.appendChild(la_hint);

                    popup.liteShow(info_card, _('404_error'));
                    return;

                };
                app.window_type = "section";
                heading.innerHTML = mn.display;
                card.appendChild(heading);
                if (b.stuff[a].about) {
                    abouT = document.createElement("div");
                    abouT.classList.add("item");
                    abouT.innerHTML = b.stuff[a].about;
                    card.appendChild(abouT);
                    document.getElementById("main").appendChild(card);
                    card = document.createElement("div");
                    card.classList.add("card");
                }

                fre = document.getElementById("home-nav-el");
                if (fre !== null) fre.parentElement.removeChild(fre);

                sect = document.createElement("div");
                sect.classList.add("el");
                sect.id = "home-nav-el";
                sect.innerHTML = mn.display;
                document.getElementsByClassName("way-path")[0].appendChild(sect);


                if (b.stuff[a]["methods"]) l = b.stuff[a]["methods"];
                else return;

                ls = [];
                l.forEach(w => {
                    ls.push("data/methods/" + app.lang + "/" + w + ".json");
                });

                xxhr(ls, function(kk) {
                    xxhry[kk].results.forEach((q, i) => xxhry[kk].results[i] = JSON.parse(q));
                    xxhry[kk].results.sort((a, b) => {
                        if (a == false) return -1;
                        if (b == false) return 0;
                        var nameA = a.display.toUpperCase();
                        var nameB = b.display.toUpperCase();
                        if (nameA < nameB) return -1;
                        if (nameA > nameB) return 1;
                        return 0;
                    });
                    xxhry[kk].results.forEach(q => {
                        if (q != false) {
                            let method = document.createElement("div");
                            method.classList.add("item");
                            method.innerHTML = q.display + '<span class="light-color"> — ' + q.purpose + '</span>';
                            method.classList.add("clickable");
                            method.onclick = () => {
                                window.location.hash = "#b-" + q.name
                            }

                            card.appendChild(method);
                        }
                    });
                });
                document.getElementById("main").appendChild(card);

            });
        })
    }
}

function chooserLang() {
    container = document.createElement("div");
    container.classList.add("multi-block-choose");

    i = 0;
    app.langs.forEach(a => {
        i++;
        r = document.createElement("div");
        r.classList.add("no-select");
        r.innerText = a.toUpperCase();
        container.appendChild(r);
        r.addEventListener("click", () => {
            toggleLang(a);
            popup.hideAll();
        });
    });

    popup.liteShow(container, _('lang'));

}

function aboutScreen() {
    popup.show(_('about_screen'), _('about'));
}

function toggleLang(a) {
    app.lang = a;
    _.prototype.loadLang();
    windower();
}

function varType(a) {
    a = a.split(" ");

    switch (a[0]) {
        case "int":
            if (a[1] == "timestamp") return _("type__timestamp");
            else
                return _("type__int");
            break;

        case "string":
            return _("type__string");
            break;

        case "bool":
            return _("type__bool");
            break;

        case "comma":
            if (a[1] === "string") return _("type__strings") + ", " + _("type__comma_separated");
            if (a[1] === "int") return _("type__ints") + ", " + _("type__comma_separated");
            break;

        case "class":

            re = "";

            o = sxhr("data/methods/" + app.lang + "/class." + a[1] + ".json");
            try {
                o = JSON.parse(o);
                name = o.display;
            } catch (e) {
                name = _("class") + " ???";
            }
            re = '<a href="#b-class.' + a[1] + '" target="_blank">' + name + "</a>";
            return re;
            break;

        case "array":
            rt = varType(a.slice(1).join(" "));
            return _("type__array") + ", " + _("that_contains") + " " + _("objects") + " " + _("of_type") + " " + rt;

        default:
            return "???";
            break;
    }
}

var patt = [];

var method = {
    open: function(a) {
        xxhr(['data/map/' + app.lang + '/methods.json', 'data/map/' + app.lang + '/sections.json', 'data/methods/' + app.lang + '/' + a + '.json'], function(jk) {
            l = xxhry[jk].results;

            w = null;
            d = null;

            l.forEach((m, i) => {
                l[i] = JSON.parse(m);
            });

            x = Object.keys(l[0].stuff);
            y = Object.values(l[0].stuff);

            y.some((e, i) => {
                if (e.methods) s = e.methods.find((z) => {
                    return z === a
                });
                else s = undefined;

                if (s) w = x[i];
            });

            if (w !== null) {

                l[1].tabs.some(e => {
                    d = e.sections.find(gl => {
                        return gl.name === w;
                    });
                    return d != undefined;
                });
            }

            if (!d) {
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

                la_hint = document.createElement("div");
                la_hint.classList.add("item");
                la_hint.classList.add("flex-hint");

                icon_holder = document.createElement("div");
                icon_holder.classList.add("icon_container");
                la_hint.appendChild(icon_holder);

                let icon = document.createElement("icon");
                icon.innerText = "error";
                icon_holder.appendChild(icon);

                let text = document.createElement("div");
                text.classList.add("hint-text");

                text.innerHTML = _('404_text', {
                    "object": _("method"),
                    "name": a.charAt(0).toUpperCase() + a.slice(1)
                });
                la_hint.appendChild(text);

                info_card.appendChild(la_hint);

                popup.liteShow(info_card, _('404_error'));
                return;

            };
            app.window_type = "method";
            engines.cleanMain();
            engines.loading();

            if (w === null) w = '???';
            if (d === null) d = w;
            else d = d.display;

            engines.cleanMain();
            engines.cleanPath();

            fre = document.getElementById("home-nav-el");
            if (fre !== null) fre.parentElement.removeChild(fre);

            sect = document.createElement("div");
            sect.classList.add("el");
            sect.id = "home-nav-el";
            sect.innerHTML = d;
            sect.onclick = function() {
                window.location.hash = "#a-" + w;
            }
            document.getElementsByClassName("way-path")[0].appendChild(sect);

            fre = document.getElementById("home-meth-el");
            if (fre !== null) fre.parentElement.removeChild(fre);

            sect = document.createElement("div");
            sect.classList.add("el");
            sect.id = "home-meth-el";
            sect.innerHTML = l[2].display;
            sect.onclick = function() {
                window.location.hash = "#b-" + l[2].name;
            }
            document.getElementsByClassName("way-path")[0].appendChild(sect);

            card = document.createElement("div");
            card.classList.add("card");

            head = document.createElement("div");
            head.classList.add("head");
            head.innerHTML = l[2].display;
            card.appendChild(head);

            ch = false;
            if (l[2].way && !l[2].purpose) {
                ch = true;
                t = l[2].way;
            } else if (l[2].purpose) {
                t = l[2].purpose;
            } else {
                t = null;
            }

            if (t !== null) {
                tip = document.createElement("div");
                tip.classList.add("item");
                tip.innerHTML = t;
                card.appendChild(tip);
            }

            document.getElementById("main").appendChild(card);

            if (!ch && l[2].way) {
                card = document.createElement("div");
                card.classList.add("card");
                tip = document.createElement("div");
                tip.classList.add("item");
                tip.innerHTML = l[2].way;
                card.appendChild(tip);
                document.getElementById("main").appendChild(card);
            }

            e = l[2];
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
                }
            ];

            if (e.request) {
                card = document.createElement("div");
                card.classList.add("card");

                head = document.createElement("div");
                head.classList.add("head");
                head.innerHTML = _('request');
                card.appendChild(head);

                p_a = [];

                if (e.request.length > 0) {

                    e.request.forEach((ty, i) => {
                        let p = patt.findIndex((o) => {
                            return o.name == ty.name
                        });
                        if (p !== -1) p_a.push([p, i]);
                    });

                    if (p_a.length > 0) {
                        h_card = document.createElement("div");
                        h_card.classList.add("card");
                        head = document.createElement("div");
                        head.classList.add("head");
                        head.innerHTML = _('hints');
                        h_card.appendChild(head);

                        p_a.forEach(rt => {
                            rt = patt[rt[0]];
                            la_hint = document.createElement("div");
                            la_hint.classList.add("item");
                            la_hint.classList.add("flex-hint")
                            if (rt.related_link) {
                                la_hint.classList.add("clickable");
                                la_hint.onclick = () => {
                                    if (rt.related_link[0] == "#") location.hash = rt.related_link;
                                    else location.href = rt.related_link
                                };
                            }

                            icon_holder = document.createElement("div");
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

                    table = document.createElement("table");
                    table.classList.add("fields-table");
                    e.request.forEach(z => {
                        row = document.createElement("tr");

                        name_f = document.createElement("td");
                        name_f.innerHTML = z.name;
                        row.appendChild(name_f);

                        info = document.createElement("td");

                        infy = document.createElement("div");
                        infy.innerHTML = z.info;

                        type = document.createElement("div");
                        type.innerHTML = varType(z.type) + (z.important ? ", <b>" + _('required_field') + "</b>" : "");

                        info.appendChild(infy);
                        info.appendChild(type);

                        row.appendChild(info);
                        table.appendChild(row);
                    });
                    card.appendChild(table);

                } else {
                    item = document.createElement("div");
                    item.classList.add("item");
                    item.innerHTML = '<span class="light-color">' + _("no_data") + '</span>';
                    card.appendChild(item);
                }
                if (e.request.length > 0) document.getElementById("main").appendChild(card);
            }

            if (e.answer) {
                card = document.createElement("div");
                card.classList.add("card");

                head = document.createElement("div");
                head.classList.add("head");
                head.innerHTML = _('response');
                card.appendChild(head);

                if (e.answer.length > 0) {

                    table = document.createElement("table");
                    table.classList.add("fields-table");
                    e.answer.forEach(z => {
                        row = document.createElement("tr");

                        name_f = document.createElement("td");
                        name_f.innerHTML = z.name;
                        row.appendChild(name_f);

                        info = document.createElement("td");

                        infy = document.createElement("div");
                        infy.innerHTML = z.info;

                        type = document.createElement("div");
                        type.innerHTML = varType(z.type) + (z.important ? ", <b>" + _('required_field') + "</b>" : "");

                        info.appendChild(infy);
                        info.appendChild(type);

                        row.appendChild(info);
                        table.appendChild(row);
                    });
                    card.appendChild(table);

                } else {
                    item = document.createElement("div");
                    item.classList.add("item");
                    item.innerHTML = '<span class="light-color">' + _("no_data") + '</span>';
                    card.appendChild(item);
                }
                document.getElementById("main").appendChild(card);
            }

        });
    }
}

function html2MD(s) {
    s = s.replace(/href="(#[^\s]+)"/, 'href="' + app.link + '$1"');
    s = s.replace(/<a href="([^\s]+)".+>(.+)<\/a>/, "[$2]($1)");
    s = s.replace(/<b>(.+)<\/b>/, "**$1**");
    s = s.replace(/<\/?ul>/, "");
    s = s.replace(/<li>(.+)<\/li>/, "* $1");
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

        p_a = [];

        if (a.request.length > 0) {

            a.request.forEach((ty, i) => {
                let p = patt.findIndex((o) => {
                    return o.name == ty.name
                });
                if (p !== -1) p_a.push([p, i]);
            });

            if (p_a.length > 0) {
                text += "## " + _("hints") + "  \n";

                p_a.forEach(rt => {
                    rt = patt[rt[0]];
                    text += "* " + (rt.related_link ? "[" : "") + html2MD(rt.text) + (rt.related_link ? "](" + (rt.related_link[0] == "#" ? app.link + rt.related_link : rt.related_link) + ")" : "") + "  \n";
                    a.request.splice(rt[1], 1);
                });
            }

            if (a.request.length > 0) text += "## " + _("request") + "  \n";
            a.request.forEach(z => {
                text += "* **" + z.name + "**  \n";
                text += html2MD(z.info) + "  \n";
                text += "_" + html2MD(varType(z.type)) + (z.important ? ", **" + _('required_field') + "**" : "") + "_  \r\n";
            });

        } else {
            text += "_" + _("no_data") + "_  \r\n";
        }
    }

    if (a.answer) {

        p_a = [];

        if (a.answer.length > 0) {
            text += "## " + _("response") + "  \n";
            a.answer.forEach(z => {
                text += "* **" + z.name + "**  \n";
                text += html2MD(z.info) + "  \n";
                text += "_" + html2MD(varType(z.type)) + (z.important ? ", **" + _('required_field') + "**" : "") + "_  \r\n";
            });

        } else {
            text += "_" + _("no_data") + "_  \r\n";
        }
    }


    return text;
}



function windower() {
    hash = window.location.hash;

    if ((linkReg = hash.match(/#a-([a-zA-Z0-9_\-]+)/i)) !== null) {
        section.open(linkReg[1]);
    } else if ((linkReg = hash.match(/#b-([a-zA-Z0-9\.a-zA-Z0-9_\-]+)/i)) !== null) {
        method.open(linkReg[1]);
    } else if (window.location.hash == "") {
        getMain();
    } else window.location.hash = "";
}

window.onhashchange = windower;