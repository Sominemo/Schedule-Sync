var app = {
    lang: "en",
    build: 61,
    version: "1.64",
    link: "https://sominemo.github.io/Schedule-Sync/",
    window: {}
}

function xhr(url, callback, onerror) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.send();

    xhr.onreadystatechange = function() {
        if (xhr.readyState != 4) return;



        if (xhr.status != 200) {
            console.error(xhr.status + ': ' + xhr.statusText);
            if (onerror !== undefined) onerror(url);
        } else {
            if (callback !== undefined) callback(xhr.responseText, url);
        }

    }
};

function sxhr(url, callback, onerror) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, false);
    xhr.send();
    if (xhr.status != 200) {
        console.error(xhr.status + ': ' + xhr.statusText);
        return "error " + xhr.status;
    } else {
        return xhr.responseText;
    }
};

var _ = function(index, p) {
    p = p || {};
    if (_.prototype.langLib.main[index] !== undefined) {
        return _.prototype.replacer(_.prototype.langLib.main[index], p);
    } else {
        console.log('Can\'t find index [' + index + '] -', app.lang);
        return '[' + index + ']';
    }
}

_.prototype.langLib = {
    main: {
        cant_load_lang: "Unable to load language pack"
    }
};


_.prototype.loadLang = function(onLoad) {
    let lng = app.lang;
    localStorage.setItem("lang", lng);
    xhr('res/lang/' + app.lang + '.json', function(a) {
        var l = JSON.parse(a);
        _.prototype.langLib = l;
        for (var i in _.prototype.langLib.auto_change) {
            [].slice.call(document.querySelectorAll('#' + i)).forEach(function(p) {
                p.innerHTML = _.prototype.langLib.auto_change[i];
            });
        };
        if (onLoad !== undefined) onLoad();
    }, function() {
        alert(_('cant_load_lang'));
        if (lng !== 'en') {
            app.lang = 'en';
            _.prototype.loadLang();
        }
    });

    xhr('data/map/main.json', function(a) {
        a = JSON.parse(a);
        app.langs = a.langs;
    });
}

_.prototype.replaceLib = {
    'replace::app_version': app.version,
    'replace::app_build': app.build
};

_.prototype.replacer = function(text, p) {
    if (typeof p === "object") {
        let k = Object.keys(p);
        k.forEach((e) => {
            text = text.split("{%" + e.toString() + "%}").join(p[e].toString());
        });
    }
    Object.keys(_.prototype.replaceLib).forEach(function(a) {
        text = text.split('%' + a + '%').join(_.prototype.replaceLib[a].toString());
    });
    return text;
}

var actions = {
    show: function(x, y, s, data) {
        actions.hideAll();
        box = document.createElement("div");
        box.classList.add("actions_box");
        box.style.top = (y + 3) + 'px';
        box.style.left = (x + 3) + 'px';
        box.id = "actions" + Math.random();
        data.forEach(function(a) {
            item = document.createElement("div");
            item.classList.add("actionsItem");
            if (a[3]) item.classList.add("frozen");
            if (!a[3]) item.onclick = function(m) {
                a[2](s);
            };

            icon = document.createElement("icon");
            icon.classList.add("ActionItemIcon");
            icon.innerHTML = a[0];
            item.appendChild(icon);

            ItemName = document.createElement("div");
            ItemName.classList.add("ActionItemName");
            ItemName.innerHTML = a[1];
            item.appendChild(ItemName);

            box.appendChild(item);

        });
        box.classList.add("getting");
        document.body.appendChild(box);
        let d = 0;
        if (box.offsetWidth >= window.innerWidth - x - 30) {
            box.style.left = (x - box.offsetWidth) + 'px';
            d = 1;
        }
        if (box.offsetHeight >= window.innerHeight - y - 100) box.style.top = (y - box.offsetHeight) + 'px';
        if ((x + box.offsetWidth > window.innerWidth && d == 0) || (x < box.offsetWidth && d == 1)) {
            box.style.width = 'calc(100% - 10px)';
            box.style.left = 0;
            box.style.right = 0;
            box.style.margin = '5px';
        }
        setInterval(function() {
            box.classList.remove("getting");
        }, 0);
    },
    hideAll: function() {
        [].slice.call(document.getElementsByClassName("actions_box")).forEach(function(a) {
            a.classList.add("dieing");
            setTimeout(function() {
                document.body.removeChild(a);
            }, 150);
        });
    }
}

document.addEventListener('scroll', function() {
    actions.hideAll();
});

window.addEventListener("click", function(a) {
    if (!a.target.matches('.--actions-clickable, .--actions-clickable-child *:not(.--actions-do-not-click), .--actions-clickable *:not(.--actions-do-not-click)')) {
        actions.hideAll();
    }
});

document.addEventListener("contextmenu", function(a) {
    actions.hideAll();
});

window.addEventListener("resize", function(a) {
    actions.hideAll();
});

var engines = {
    copy: function(s) {
        window.getSelection().selectAllChildren(s);
        let CopyResult = document.execCommand('copy');
        if (CopyResult == true) alert(_('copied'));
        else alert(_('unable2copy'));
        if (window.getSelection) window.getSelection().removeAllRanges();
        else if (document.selection) document.selection.empty();
    },
    copyLink: function() {
        let m = document.getElementById("service");
        m.innerText = window.location.href;
        engines.copy(m);
        m.innerText = '';
    },
    getMD: function() {
        r = generateMD_method();
        let linkid = "d-" + Math.random();

        let info_card = document.createElement("div");
        info_card.classList.add("card");

        la_hint = document.createElement("div");
        la_hint.classList.add("item");
        la_hint.classList.add("flex-hint");

        icon_holder = document.createElement("div");
        icon_holder.classList.add("icon_container");
        la_hint.appendChild(icon_holder);

        let icon = document.createElement("icon");
        icon.innerText = "format_indent_increase";
        icon_holder.appendChild(icon);

        let text = document.createElement("div");
        text.classList.add("hint-text");

        sdata = 'data:text/plain;charset=utf-8,' + encodeURIComponent(r);

        text.innerHTML = _("md_file_successfully_generated", {
            download_link: sdata,
            file_name: app.window.method_json.display + ".md",
            link_id: linkid
        });
        la_hint.appendChild(text);

        info_card.appendChild(la_hint);

        cb = document.createElement("p");
        cb.innerHTML = _("files_contents_below") + ":";
        info_card.appendChild(cb);

        cod = document.createElement("pre");
        cod.classList.add("grey");
        cod.innerText = r;

        info_card.appendChild(cod);

        popup.liteShow(info_card, _("result"));
        document.getElementById(linkid).click();

        try {
            navigator.msSaveBlob(Blob([r], {type: 'text/plain'}), app.window.method_json.display + ".md");
        } catch(e) {

        }

        return true;
    },
    loading: function() {
        document.getElementById("main").innerHTML = '<div class="centration-div"> <svg width="56px" height="56px" version="1" viewBox="0 0 28 28" xmlns="http://www.w3.org/2000/svg"> <style type="text/css">.qp-circular-loader { width:28px; height:28px; } .qp-circular-loader-path { stroke-dasharray: 58.9; stroke-dashoffset: 58.9; } .qp-circular-loader, .qp-circular-loader * { -webkit-transform-origin: 50% 50%; } /* Rotating the whole thing */ @-webkit-keyframes rotate { from {-webkit-transform: rotate(0deg);} to {-webkit-transform: rotate(360deg);} } .qp-circular-loader { -webkit-animation-name: rotate; -webkit-animation-duration: 1568.63ms; -webkit-animation-iteration-count: infinite; -webkit-animation-timing-function: linear; } /* Filling and unfilling the arc */ @-webkit-keyframes fillunfill { from { stroke-dashoffset: 58.8; } 50% { stroke-dashoffset: 0; } to { stroke-dashoffset: -58.4; } } @-webkit-keyframes rot { from { -webkit-transform: rotate(0deg); } to { -webkit-transform: rotate(-360deg); } } @-webkit-keyframes colors { from { stroke: var(--main-color); } to { stroke: var(--main-color); } } .qp-circular-loader-path { -webkit-animation-name: fillunfill, rot, colors; -webkit-animation-duration: 1333ms, 5332ms, 5332ms; -webkit-animation-iteration-count: infinite, infinite, infinite; -webkit-animation-timing-function: cubic-bezier(0.4, 0.0, 0.2, 1), steps(4), linear; -webkit-animation-play-state: running, running, running; -webkit-animation-fill-mode: forwards; }</style> <g class="qp-circular-loader"> <path class="qp-circular-loader-path" d="M 14,1.5 A 12.5,12.5 0 1 1 1.5,14" fill="none" stroke-linecap="square" stroke-width="3"/> </g> </svg> </div>';
    },
    cleanMain: function() {
        document.getElementById("main").innerHTML = '';
        app.window = {};
    },
    cleanPath: function() {
        document.getElementsByClassName("way-path")[0].innerHTML = '<div class="el" id="home-path-el" onclick="getMain()">' + _('home') + '</div>';
    }
};

var popup = {
    displayed: [],
    liteShow: function(text, name) {
        contents = document.createElement("div");
        contents.classList.add("popup-container");
        random = Math.random();

        sh = document.createElement("div");
        sh.classList.add("settings_header");

        tt = document.createElement("div");
        tt.classList.add("settings_title");
        tt.innerHTML = name;
        sh.appendChild(tt);

        clb = document.createElement("icon");
        clb.classList.add("addripple");
        clb.classList.add("blackripple");
        clb.innerText = "close";
        clb.onclick = function() {
            popup.hide(random)
        };
        sh.appendChild(clb);

        contents.appendChild(sh);

        contents.appendChild(text);

        lpopup = contents;

        document.getElementById('popups').appendChild(lpopup);
        popup.displayed.push(random);
        document.getElementById('popups').classList.add('active');
        document.getElementById('popups').onclick = function(a) {
            if (!a.target.matches("#popups *")) {
                let clickFunc = a.target.getAttribute('onclick');
                if (clickFunc === null || clickFunc.indexOf('popup.hide') == -1) {
                    popup.hideAll();
                }
            }
        }
        setTimeout(function() {
            document.getElementById('popups').style.opacity = 1;
        }, 50);

    },
    show: function(text, name, params) {
        params = params || {};
        if (params.bgImage && !params.imageWasLoaded) {
            let bgImage = new Image;
            bgImage.src = params.bgImage;
            params.imageWasLoaded = 1;
            bgImage.onload = function() {
                popup.show(text, name, params);
            };
            return;
        }
        var random_id = Math.random();
        lpopup = document.createElement('div');
        lpopup.className = 'popup-container';
        lpopup.id = "popup-" + random_id;
        if (name === undefined) name = '';
        let body = '';
        if (params.bgImage && params.bgColor) {
            lpopup.style.padding = 0;
            lpopup.style.borderRadius = 0;
        }
        if (params.bgImage && params.bgColor) body += '<div class="headStyledPopup" style="background-image: url(\'' + params.bgImage + '\'); background-repeat: no-repeat; background-color: ' + params.bgColor + '; height: 150px; color: white; ' + (!params.fullSizeBg ? 'background-size: auto 60%; background-position-x: 0; background-position-y: 65%; padding: 10px; ' : 'background-size: cover; padding: 15px;') + ' padding-bottom: 10%; text-shadow: 0 1px 4px rgba(0,0,0,0.7);">';
        body += '<div class="settings_header"><div class="settings_title">' + name + '</div><icon class="addripple blackripple" onclick="popup.hide(' + random_id + ')">close</icon></div>';
        if (params.bgImage && params.bgColor) body += '</div>';
        body += '<div class="contents' + (params.beautifyPtag ? ' bpt-padding' : '') + '"' + (params.bgImage && params.bgColor ? ' style="padding: 20px; max-width: calc(100% - 40px);"' : '') + '>' + text.split('%popup_id%').join(random_id);
        if (params.buttons) body += '<div class="settingsDoc">' + params.buttons.split('%popup_id%').join(random_id) + '</div>';
        body += '</div>';
        lpopup.innerHTML = body;
        document.getElementById('popups').appendChild(lpopup);
        popup.displayed.push(random_id);
        document.getElementById('popups').classList.add('active');
        document.getElementById('popups').onclick = function(a) {
            if (!a.target.matches("#popups *")) {
                let clickFunc = a.target.getAttribute('onclick');
                if (clickFunc === null || clickFunc.indexOf('popup.hide') == -1) {
                    popup.hideAll();
                }
            }
        }
        setTimeout(function() {
            document.getElementById('popups').style.opacity = 1;
            if (params && params.onReadyFunc) params.onReadyFunc();
        }, 50);
    },

    hide: function(id) {
        doc = document.getElementById("popup-" + id);
        let theid = popup.displayed.indexOf(id);
        popup.displayed.splice(theid, 1);
        if (popup.displayed.length == 0) {

            document.getElementById('popups').style.opacity = '';
            setTimeout(function() {
                doc.parentElement.removeChild(doc);
                document.getElementById('popups').innerHTML = '';
                document.getElementById('popups').classList.remove('active');
            }, 200);
        } else {
            doc.parentElement.removeChild(doc);
        }
    },

    hideAll: function() {
        document.getElementById('popups').style.opacity = '';
        setTimeout(function() {
            document.getElementById('popups').innerHTML = '';
            document.getElementById('popups').classList.remove('active');
            popup.displayed = [];
        }, 200);
    }

}