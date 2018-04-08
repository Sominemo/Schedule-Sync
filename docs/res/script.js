// Generator XXR
document.getElementById("menu-action").addEventListener("click", function(a) {
    actions.show(a.target.offsetLeft + 15*2, a.target.offsetTop + 10.75, a.target, [
        ["share", _('copy_link'), engines.copyLink],
        ["translate", _('lang'), chooserLang],
        ["format_indent_increase", _('gen_md'), engines.getMD, true],
        ["info_outline", _('about'), aboutScreen]
    ]);
    a.preventDefault();
})

_.prototype.loadLang();

function getMain() {
    if (window.location.hash != "") window.location.hash = "";
    app.window = "main";
    document.getElementsByClassName("way-path")[0].innerHTML = '<div class="el" id="home-path-el" onclick="getMain()">'+_('home')+'</div>';
    xhr('data/map/main.json', function(a) {
        a = JSON.parse(a);
        
        xhr('data/map/'+app.lang+'/sections.json', function(b) {
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
                    l.onclick = () => {window.location.hash = "#section-"+m.name};
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
        xhr(e, function(m){
            xxhry[g]["done"]++;
            xxhry[g]["results"].push(m);
            if (xxhry[g]["done"] === a.length) c(g);
        }, function() {
            xxhry[g]["done"]++;
            xxhry[g]["results"].push(false);
            if (xxhry[g]["done"] === a.length) c(g);
        })
    });
}

var section = {
    open: function(a) {
        app.window = "section";
        xhr('data/map/'+app.lang+'/sections.json', function(r) {
            r = JSON.parse(r);
            xhr('data/map/'+app.lang+'/methods.json', function(b) {
                b = JSON.parse(b);
                engines.cleanMain();
    
                let card = document.createElement("div");
                card.classList.add("card");

                let heading = document.createElement("div");
                heading.classList.add("head");
                r.tabs.some(e => {
                    mn = e.sections.find(gl => {return gl.name == a});
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

    
                if (b.stuff[a]["methods"]) l = b.stuff[a]["methods"]; else return;
    
                ls = [];
                l.forEach(w => {
                    ls.push("data/methods/"+app.lang+"/"+w+".json");
                });

                xxhr(ls, function(kk){
                    xxhry[kk].results.forEach(q => {
                            if (q != false) {
                                q = JSON.parse(q);
                                method = document.createElement("div");
                                method.classList.add("item");
                                method.innerHTML = q.name+'<span class="light-color"> — '+q.purpose+'</span>';
                                method.classList.add("clickable");

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
        r.addEventListener("click", () => {toggleLang(a)});
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



function windower() {
    hash = window.location.hash;
    
    if ((linkReg = hash.match(/#section-([a-zA-Z0-9]+)/i)) !== null) {
        section.open(linkReg[1]);
    } else if (window.location.hash == "") {
        getMain();
    }

    else window.location.hash = "";
}

window.onload = window.onhashchange = windower;