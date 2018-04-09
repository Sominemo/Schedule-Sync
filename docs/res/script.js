// Generator XXR
document.getElementById("menu-action").addEventListener("click", function(a) {
    actions.show(a.target.offsetLeft + 15 * 2, a.target.offsetTop + 10.75, a.target, [
        ["share", _('copy_link'), engines.copyLink],
        ["translate", _('lang'), chooserLang],
        ["format_indent_increase", _('gen_md'), engines.getMD, (app.window !== "method" ? true : false)],
        ["info_outline", _('about'), aboutScreen]
    ]);
    a.preventDefault();
})

_.prototype.loadLang();

function getMain() {
    if (window.location.hash != "") window.location.hash = "";
    app.window = "main";
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
            if (xxhry[g]["done"] === a.length) { c(g); }
        }, function(l) {
            xxhry[g]["done"]++;
            ni = a.findIndex((er) => er === l);
            xxhry[g]["results"][ni] = false;
            if (xxhry[g]["done"] === a.length) { c(g); }
        })
    });
}

var section = {
    open: function(a) {
        app.window = "section";
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
                    xxhry[kk].results.forEach(q => {
                        if (q != false) {
                            q = JSON.parse(q);
                            let method = document.createElement("div");
                            method.classList.add("item");
                            method.innerHTML = q.name + '<span class="light-color"> — ' + q.purpose + '</span>';
                            method.classList.add("clickable");
                            method.onclick = () => {window.location.hash = "#b-"+q.name}

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
            toggleLang(a)
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

var method = {
    open: function(a) {
        app.window = "method";
        engines.cleanMain();
        engines.loading();
        xxhr(['data/map/' + app.lang + '/methods.json', 'data/map/' + app.lang + '/sections.json', 'data/methods/' + app.lang + '/' + a + '.json'], function(jk) {
            l = xxhry[jk].results;

            w = null;
            d = null;

            l.forEach((m,i) => {l[i] = JSON.parse(m);});

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
                sect.onclick = function() {window.location.hash = "#a-"+w;}
                document.getElementsByClassName("way-path")[0].appendChild(sect);
            
            fre = document.getElementById("home-meth-el");
                if (fre !== null) fre.parentElement.removeChild(fre);

                sect = document.createElement("div");
                sect.classList.add("el");
                sect.id = "home-meth-el";
                sect.innerHTML = l[2].display;
                sect.onclick = function() {window.location.hash = "#b-"+l[2].name;}
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

                var patt = [
                    {
                        "name": "token",
                        "icon": "vpn_key",
                        "text": _("token_att_text"),
                        "related_link": "#b-token.get"
                    }
                ];

                e = l[2];

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
                            let p = patt.findIndex((o) => {return o.name == ty.name});
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
                            
                            la_hint = document.createElement("div");
                            la_hint.classList.add("item");
                            if (rt.related_link) la_hint.classList.add("clickable");

                            icon_holder = document.createElement("div");
                            icon_holder.classList.add("icon_container");
                            

                            e.request.splice(rt[1], 0);
                        });


                    }



                    } else {
                        item = document.createElement("div");
                        item.classList.add("item");
                        item.innerHTML = '<span class="light-color">'+_("no_data")+'</span>';
                        card.appendChild(item);
                    }
                    document.getElementById("main").appendChild(card);
                }

        });
    }
}



function windower() {
    hash = window.location.hash;

    if ((linkReg = hash.match(/#a-([a-zA-Z0-9]+)/i)) !== null) {
        section.open(linkReg[1]);
    } else if((linkReg = hash.match(/#b-([a-zA-Z0-9\.a-zA-Z0-9]+)/i)) !== null) {
        method.open(linkReg[1]);
    } else if (window.location.hash == "") {
        getMain();
    } else window.location.hash = "";
}

window.onload = window.onhashchange = windower;