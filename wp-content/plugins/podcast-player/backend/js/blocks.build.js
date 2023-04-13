!function(){"use strict";var e=wp.components.CheckboxControl,t=function(t){var a=t.listItems,n=t.selected,l=t.onItemChange,o=t.label;return wp.element.createElement("div",{className:"components-base-control"},wp.element.createElement("label",{class:"components-base-control__label"},o),wp.element.createElement("ul",{className:"multibox__checklist"},a.map((function(t){return wp.element.createElement("li",{key:t.value,className:"multibox__checklist-item"},wp.element.createElement(e,{label:t.label,checked:n.includes(t.value),onChange:function(){l(t.value)}}))}))))},a=wp.components.CheckboxControl,n=function(e){var t=e.listItems,n=e.selected,l=e.onItemChange,o=e.label;return wp.element.createElement("div",{className:"components-base-control"},wp.element.createElement("label",{class:"components-base-control__label"},o),wp.element.createElement("ul",{className:"multibox__checklist"},t.map((function(e){return wp.element.createElement("li",{key:e.value,className:"multibox__checklist-item"},wp.element.createElement(a,{label:(t=e.label,o=document.createElement("textarea"),o.innerHTML=t,o.value),checked:n.includes(e.value),onChange:function(){l(e.value)},disabled:n.includes("")&&""!==e.value,className:n.includes("")&&""!==e.value?"checkbox-disabled":""}));var t,o}))))};function l(e){return l="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},l(e)}function o(e){return function(e){if(Array.isArray(e))return r(e)}(e)||function(e){if("undefined"!=typeof Symbol&&null!=e[Symbol.iterator]||null!=e["@@iterator"])return Array.from(e)}(e)||function(e,t){if(e){if("string"==typeof e)return r(e,t);var a=Object.prototype.toString.call(e).slice(8,-1);return"Object"===a&&e.constructor&&(a=e.constructor.name),"Map"===a||"Set"===a?Array.from(e):"Arguments"===a||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(a)?r(e,t):void 0}}(e)||function(){throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function r(e,t){(null==t||t>e.length)&&(t=e.length);for(var a=0,n=new Array(t);a<t;a++)n[a]=e[a];return n}function i(e,t,a){return t in e?Object.defineProperty(e,t,{value:a,enumerable:!0,configurable:!0,writable:!0}):e[t]=a,e}function c(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function s(e,t){for(var a=0;a<t.length;a++){var n=t[a];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}function p(e,t){return p=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(e,t){return e.__proto__=t,e},p(e,t)}function u(e,t){if(t&&("object"===l(t)||"function"==typeof t))return t;if(void 0!==t)throw new TypeError("Derived constructors may only return object or undefined");return d(e)}function d(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}function m(e){return m=Object.setPrototypeOf?Object.getPrototypeOf.bind():function(e){return e.__proto__||Object.getPrototypeOf(e)},m(e)}var h=wp.i18n.__,y=wp.element,b=y.Component,f=y.Fragment,g=wp.editor,v=g.MediaUpload,w=g.PanelColorSettings,S=wp.apiFetch,E=wp.components,C=E.Dashicon,k=E.SelectControl,P=E.PanelBody,L=E.Button,x=E.Disabled,T=E.Placeholder,_=E.RangeControl,A=E.TextControl,D=E.TextareaControl,F=E.ToggleControl,R=E.Toolbar,M=wp.serverSideRender,O=wp.blockEditor,B=O.BlockControls,I=O.InspectorControls,U=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),Object.defineProperty(e,"prototype",{writable:!1}),t&&p(e,t)}(g,e);var a,l,r,y,b=(r=g,y=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}(),function(){var e,t=m(r);if(y){var a=m(this).constructor;e=Reflect.construct(t,arguments,a)}else e=t.apply(this,arguments);return u(this,e)});function g(){var e;c(this,g);var t=!(e=b.apply(this,arguments)).props.attributes.feedURL&&"feed"===e.props.attributes.fetchMethod||!e.props.attributes.audioSrc&&"link"===e.props.attributes.fetchMethod;e.state={editing:t,fontFamilies:[],postTypes:[],taxonomies:[],termsList:[],episodeList:[],seasonList:[],categoryList:[],feedIndex:[]};var a=window.ppmejsSettings||{};return e.isPremium=a.isPremium,e.fetching=!1,e.toggleAttribute=e.toggleAttribute.bind(d(e)),e.onSubmitURL=e.onSubmitURL.bind(d(e)),e}return a=g,(l=[{key:"apiDataFetch",value:function(e,t){var a=this;this.fetching?setTimeout(this.apiDataFetch.bind(this,e,t),200):(this.fetching=!0,S({path:"/podcastplayer/v1/"+t}).then((function(t){var n=Object.keys(t);n=n.map((function(e){return{label:t[e],value:e}})),a.setState(i({},e,n)),a.fetching=!1})).catch((function(t){a.setState(i({},e,[])),a.fetching=!1,console.log(t)})))}},{key:"componentDidMount",value:function(){if(this.apiDataFetch("feedIndex","fIndex"),this.isPremium){var e=this.props.attributes,t=e.postType,a=e.fetchMethod;this.apiDataFetch("postTypes","posttypes"),this.apiDataFetch("fontFamilies","fontfamily"),"link"!==a&&this.updateElist(),"feed"===a&&(this.updateSlist(),this.updateCatlist()),t&&(this.updateTaxonomy(),this.updateTerms())}}},{key:"componentDidUpdate",value:function(e){if(this.isPremium){var t=e.attributes,a=t.postType,n=t.taxonomy,l=t.fontFamily,o=t.terms,r=t.sortBy,i=t.filterBy,c=t.fetchMethod,s=t.feedURL,p=t.catlist,u=t.slist,d=this.props.attributes,m=d.postType,h=d.taxonomy,y=d.fontFamily,b=d.terms,f=d.sortBy,g=d.filterBy,v=d.fetchMethod,w=d.feedURL,S=d.slist,E=d.catlist;a!==m&&this.updateTaxonomy(),n!==h&&this.updateTerms(),l!==y&&this.updateFonts(),n===h&&o===b&&r===f&&i===g&&c===v&&s===w&&p===E&&u===S||this.updateElist(),(c!==v&&"feed"===v||s!==w)&&(this.updateSlist(),this.updateCatlist())}}},{key:"updateTaxonomy",value:function(){var e=this.props.attributes.postType;e?this.apiDataFetch("taxonomies","taxonomies/"+e):this.setState({taxonomies:[],termsList:[]})}},{key:"updateTerms",value:function(){var e=this.props.attributes.taxonomy;e?this.apiDataFetch("termsList","terms/"+e):this.setState({termsList:[]})}},{key:"updateElist",value:function(){var e=this.props.attributes,t=e.fetchMethod,a=e.feedURL,n=e.postType,l=e.taxonomy,o=e.terms,r=e.sortBy,i=e.filterBy,c=e.slist,s=e.catlist;if("feed"===t&&""!==a){var p="",u=!!c&&c.filter(Boolean),d=!!s&&s.filter(Boolean);u&&u.length&&(p+="&seasons="+u.join()),d&&d.length&&(p+="&categories="+d.join()),this.apiDataFetch("episodeList","fElist?feedURL="+encodeURIComponent(a)+p)}else if("post"===t){var m="";l&&o&&o.length&&(m+="&taxonomy="+l+"&terms="+o.join()),r&&(m+="&sortBy="+r),i&&(m+="&filterBy="+i),this.apiDataFetch("episodeList","pElist?postType="+n+m)}else this.setState({episodeList:[]})}},{key:"updateSlist",value:function(){var e=this.props.attributes,t=e.fetchMethod,a=e.feedURL;"feed"===t&&""!==a?this.apiDataFetch("seasonList","fSlist?feedURL="+a):this.setState({seasonList:[]})}},{key:"updateCatlist",value:function(){var e=this.props.attributes,t=e.fetchMethod,a=e.feedURL;"feed"===t&&""!==a?this.apiDataFetch("categoryList","fcatlist?feedURL="+a):this.setState({categoryList:[]})}},{key:"updateFonts",value:function(){var e=this.props.attributes.fontFamily,t=this.state.fontFamilies;if(e){var a=t.filter((function(t){return e===t.value}));if(a.length){var n=a[0].label.split(" ").join("+");if(0===jQuery("link#podcast-player-fonts-css-temp").length){var l=jQuery("<link>",{id:"podcast-player-fonts-css-temp",href:"//fonts.googleapis.com/css?family="+n,rel:"stylesheet",type:"text/css"});jQuery("link:last").after(l)}else{var o=jQuery("link#podcast-player-fonts-css-temp"),r=o.attr("href");o.attr("href",r+"%7C"+n)}}}}},{key:"toggleAttribute",value:function(e){var t=this;return function(){var a=t.props.attributes[e];(0,t.props.setAttributes)(i({},e,!a))}}},{key:"onSubmitURL",value:function(e){e.preventDefault();var t=this.props.attributes,a=t.fetchMethod,n=t.feedURL,l=t.audioSrc;"feed"===a?n&&this.setState({editing:!1}):"link"===a&&l&&this.setState({editing:!1})}},{key:"navMenuSelect",value:function(){var e=window.podcastPlayerData.menu||{};return(e=Array.from(e)).push({label:"- Select Menu -",value:""}),e.map((function(e){return{label:e.label,value:e.value}}))}},{key:"render",value:function(){var e=this,a=this.props.attributes,l=a.feedURL,r=a.sortBy,i=a.filterBy,c=a.number,s=a.teaserText,p=a.excerptLength,u=a.excerptUnit,d=a.podcastMenu,m=a.mainMenuItems,y=a.coverImage,b=a.description,g=a.accentColor,S=a.displayStyle,E=a.aspectRatio,O=a.cropMethod,U=a.gridColumns,N=a.fetchMethod,j=a.postType,H=a.taxonomy,q=a.terms,z=a.podtitle,Q=a.audioSrc,G=a.audioTitle,V=a.audioLink,W=a.headerDefault,Y=a.listDefault,$=a.hideHeader,J=a.hideTitle,K=a.hideCover,X=a.hideDesc,Z=a.hideSubscribe,ee=a.hideSearch,te=a.hideAuthor,ae=a.hideContent,ne=a.hideLoadmore,le=a.hideDownload,oe=a.ahideDownload,re=a.hideSocial,ie=a.hideFeatured,ce=a.ahideSocial,se=a.audioMsg,pe=a.playFreq,ue=a.msgStart,de=a.msgTime,me=a.msgText,he=a.bgColor,ye=a.txtColor,be=a.fontFamily,fe=a.appleSub,ge=a.googleSub,ve=a.spotifySub,we=a.breakerSub,Se=a.castboxSub,Ee=a.castroSub,Ce=a.iheartSub,ke=a.overcastSub,Pe=a.pocketcastsSub,Le=a.podcastaddictSub,xe=a.podchaserSub,Te=a.radiopublicSub,_e=a.soundcloudSub,Ae=a.stitcherSub,De=a.tuneinSub,Fe=a.youtubeSub,Re=a.bullhornSub,Me=a.podbeanSub,Oe=a.playerfmSub,Be=a.elist,Ie=a.slist,Ue=a.catlist,Ne=a.edisplay,je=this.state,He=je.postTypes,qe=je.taxonomies,ze=je.termsList,Qe=je.episodeList,Ge=je.seasonList,Ve=je.categoryList,We=je.fontFamilies,Ye=je.feedIndex,$e=this.props.setAttributes,Je=this.navMenuSelect(),Ke=window.podcastPlayerData.style||{label:"Default",value:""},Xe=function(e,t){var a=window.podcastPlayerData.stSup||!1;return!(void 0===S||!a)&&!!a[e]&&a[e].includes(t)},Ze=[{value:"",label:h("No Cropping","podcast-player")},{value:"land1",label:h("Landscape (4:3)","podcast-player")},{value:"land2",label:h("Landscape (3:2)","podcast-player")},{value:"port1",label:h("Portrait (3:4)","podcast-player")},{value:"port2",label:h("Portrait (2:3)","podcast-player")},{value:"wdscrn",label:h("Widescreen (16:9)","podcast-player")},{value:"squr",label:h("Square (1:1)","podcast-player")}],et=[{value:"topleftcrop",label:h("Top Left Cropping","podcast-player")},{value:"topcentercrop",label:h("Top Center Cropping","podcast-player")},{value:"centercrop",label:h("Center Cropping","podcast-player")},{value:"bottomcentercrop",label:h("Bottom Center Cropping","podcast-player")},{value:"bottomleftcrop",label:h("Bottom Left Cropping","podcast-player")}],tt=function(t){$e({fetchMethod:t,elist:[""],slist:[""],catlist:[""],edisplay:""}),"post"===t?e.setState({editing:!1}):e.setState({editing:!0})};if(this.state.editing)return wp.element.createElement(f,null,wp.element.createElement(T,{icon:"rss",label:"RSS"},wp.element.createElement("form",{onSubmit:this.onSubmitURL},!!("feed"===N&&Ye&&Array.isArray(Ye)&&Ye.length)&&wp.element.createElement("div",{style:{width:"100%"}},wp.element.createElement(k,{value:l,onChange:function(e){return $e({feedURL:e,elist:[""],slist:[""],catlist:[""],edisplay:""})},options:Ye,style:{maxWidth:"none"}}),wp.element.createElement("span",{style:{width:"100%",textAlign:"center",marginBottom:"10px",display:"block"}},"OR")),"feed"===N&&wp.element.createElement("div",{style:{width:"100%"}},wp.element.createElement(A,{placeholder:h("Enter URL here…","podcast-player"),value:l,onChange:function(e){return $e({feedURL:e,elist:[""],slist:[""],catlist:[""],edisplay:""})},className:"components-placeholder__input"})),"link"===N&&wp.element.createElement(A,{placeholder:h("Enter Audio/Video Link (i.e, mp3, ogg, m4a etc.)","podcast-player"),value:Q,onChange:function(e){return $e({audioSrc:e})},className:"components-placeholder__input"}),wp.element.createElement(L,{type:"submit",style:{backgroundColor:"#f7f7f7"}},h("Show Podcast","podcast-player")))),wp.element.createElement(I,null,!!this.isPremium&&wp.element.createElement(P,{initialOpen:!0,title:h("Setup Fetching Method","podcast-player")},wp.element.createElement(k,{label:h("Fetch Podcast Episodes","podcast-player"),value:N,onChange:tt,options:[{value:"feed",label:h("from Feed","podcast-player")},{value:"post",label:h("from Post","podcast-player")},{value:"link",label:h("from Audio/Video URL","podcast-player")}]}))));var at=[{icon:"edit",title:h("Edit RSS URL","podcast-player"),onClick:function(){return e.setState({editing:!0})}}];return wp.element.createElement(f,null,wp.element.createElement(B,null,wp.element.createElement(R,{controls:at})),wp.element.createElement(I,null,!!this.isPremium&&wp.element.createElement(P,{initialOpen:!0,title:h("Setup Fetching Method","podcast-player")},wp.element.createElement(k,{label:h("Fetch Podcast Episodes","podcast-player"),value:N,onChange:tt,options:[{value:"feed",label:h("from Feed","podcast-player")},{value:"post",label:h("from Post","podcast-player")},{value:"link",label:h("from Audio/Video URL","podcast-player")}]}),He&&"post"===N&&wp.element.createElement(k,{label:h("Select Post Type","podcast-player"),value:j,options:He,onChange:function(e){return function(e){$e({terms:[]}),$e({taxonomy:""}),$e({postType:e})}(e)}}),j&&!!qe.length&&"post"===N&&wp.element.createElement(k,{label:h("Get items by Taxonomy","podcast-player"),value:H,options:qe,onChange:function(e){return function(e){$e({terms:[]}),$e({taxonomy:e})}(e)}}),!!ze.length&&"post"===N&&wp.element.createElement(t,{listItems:ze,selected:q,onItemChange:function(e){var t=q.indexOf(e);$e(-1===t?{terms:[].concat(o(q),[e])}:{terms:q.filter((function(t){return t!==e}))})},label:h("Select Taxonomy Terms","podcast-player")}),"link"===N&&wp.element.createElement(A,{label:h("Episode Title","podcast-player"),value:G,onChange:function(e){return $e({audioTitle:e})}}),"link"===N&&wp.element.createElement(A,{label:h("Podcast episode link for social sharing (optional)","podcast-player"),value:V,onChange:function(e){return $e({audioLink:e})}}),"link"===N&&wp.element.createElement(F,{label:h("Hide Episode Download Link","podcast-player"),checked:!!oe,onChange:function(e){return $e({ahideDownload:e})}}),"link"===N&&wp.element.createElement(F,{label:h("Hide Social Share Links","podcast-player"),checked:!!ce,onChange:function(e){return $e({ahideSocial:e})}})),wp.element.createElement(P,{initialOpen:!1,title:h("Change Podcast Content","podcast-player")},this.isPremium&&"post"===N&&wp.element.createElement(A,{label:h("Podcast Title","podcast-player"),value:z,onChange:function(e){return $e({podtitle:e})}}),wp.element.createElement(v,{onSelect:function(e){return $e({coverImage:e.url})},type:"image",value:y,render:function(e){var t=e.open;return wp.element.createElement(L,{className:"pp-cover-btn",onClick:t},y?wp.element.createElement("img",{className:"ppe-cover-image",src:y,alt:h("Cover Image","podcast-player")}):wp.element.createElement("div",{className:"no-image"},wp.element.createElement(C,{icon:"format-image"}),h("Upload Cover Image","podcast-player")))}}),y&&wp.element.createElement(L,{className:"remove-pp-cover",onClick:function(){return $e({coverImage:""})}},h("Remove Cover Image","podcast-player")),wp.element.createElement(D,{label:h("Brief Description","podcast-player"),help:h("Change Default Podcast Description","podcast-player"),value:b,onChange:function(e){return $e({description:e})}}),"link"!==N&&wp.element.createElement(_,{label:h("Number of episodes to show at a time","podcast-player"),value:c,onChange:function(e){return $e({number:e})},min:1,max:1e3}),Xe(S,"excerpt")&&"link"!==N&&wp.element.createElement(k,{label:h("Teaser Text","podcast-player"),value:s,onChange:function(e){return $e({teaserText:e})},options:[{value:"",label:h("Show Excerpt","podcast-player")},{value:"full",label:h("Show Full Content","podcast-player")},{value:"none",label:h("Do not Show Teaser Text","podcast-player")}]}),Xe(S,"excerpt")&&""===s&&"link"!==N&&wp.element.createElement(_,{label:h("Excerpt Length","podcast-player"),value:p,onChange:function(e){return $e({excerptLength:e})},min:0,max:200}),Xe(S,"excerpt")&&""===s&&"link"!==N&&wp.element.createElement(k,{label:h("Excerpt Length Unit","podcast-player"),value:u,onChange:function(e){return $e({excerptUnit:e})},options:[{value:"",label:h("Number of words","podcast-player")},{value:"char",label:h("Number of characters","podcast-player")}]})),wp.element.createElement(P,{initialOpen:!1,title:h("Subscription Buttons","podcast-player")},(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Apple Subscription Link","podcast-player"),value:fe,onChange:function(e){return $e({appleSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Google Subscription Link","podcast-player"),value:ge,onChange:function(e){return $e({googleSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Spotify Subscription Link","podcast-player"),value:ve,onChange:function(e){return $e({spotifySub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Breaker Subscription Link","podcast-player"),value:we,onChange:function(e){return $e({breakerSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Castbox Subscription Link","podcast-player"),value:Se,onChange:function(e){return $e({castboxSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Castro Subscription Link","podcast-player"),value:Ee,onChange:function(e){return $e({castroSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("iHeart Radio Subscription Link","podcast-player"),value:Ce,onChange:function(e){return $e({iheartSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Overcast Subscription Link","podcast-player"),value:ke,onChange:function(e){return $e({overcastSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Pocket Casts Subscription Link","podcast-player"),value:Pe,onChange:function(e){return $e({pocketcastsSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Podcast Addict Subscription Link","podcast-player"),value:Le,onChange:function(e){return $e({podcastaddictSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Podchaser Subscription Link","podcast-player"),value:xe,onChange:function(e){return $e({podchaserSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Radio Public Subscription Link","podcast-player"),value:Te,onChange:function(e){return $e({radiopublicSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("SoundCloud Subscription Link","podcast-player"),value:_e,onChange:function(e){return $e({soundcloudSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Stitcher Subscription Link","podcast-player"),value:Ae,onChange:function(e){return $e({stitcherSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Tune In Subscription Link","podcast-player"),value:De,onChange:function(e){return $e({tuneinSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("YouTube Subscription Link","podcast-player"),value:Fe,onChange:function(e){return $e({youtubeSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Bull Horn Subscription Link","podcast-player"),value:Re,onChange:function(e){return $e({bullhornSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("Podbean Subscription Link","podcast-player"),value:Me,onChange:function(e){return $e({podbeanSub:e})}}),(!d||!m)&&wp.element.createElement(A,{label:h("Add a Podcast Subscription link","podcast-player"),placeholder:h("PlayerFM Subscription Link","podcast-player"),value:Oe,onChange:function(e){return $e({playerfmSub:e})}}),wp.element.createElement(k,{label:h("Podcast Subscription Menu","podcast-player"),value:d,onChange:function(e){return $e({podcastMenu:e})},options:Je}),!!d&&!!m&&wp.element.createElement(_,{label:h("Number of Primary Subscription Links","podcast-player"),value:m,onChange:function(e){return $e({mainMenuItems:e})},min:0,max:20})),"link"!==N&&wp.element.createElement(P,{initialOpen:!1,title:h("Show/Hide Player Items","podcast-player")},(!S||"legacy"===S||"modern"===S)&&wp.element.createElement(F,{label:h("Show Podcast Header by Default","podcast-player"),checked:!!W,onChange:function(e){return $e({headerDefault:e})}}),(!S||"legacy"===S||"modern"===S)&&wp.element.createElement(F,{label:h("Show episodes list by default on mini player.","podcast-player"),checked:!!Y,onChange:function(e){return $e({listDefault:e})}}),wp.element.createElement(F,{label:h("Hide Podcast Header","podcast-player"),checked:!!$,onChange:function(e){return $e({hideHeader:e})}}),!$&&wp.element.createElement(F,{label:h("Hide cover image","podcast-player"),checked:!!K,onChange:function(e){return $e({hideCover:e})}}),!$&&wp.element.createElement(F,{label:h("Hide Podcast Title","podcast-player"),checked:!!J,onChange:function(e){return $e({hideTitle:e})}}),!$&&wp.element.createElement(F,{label:h("Hide Podcast Description","podcast-player"),checked:!!X,onChange:function(e){return $e({hideDesc:e})}}),!$&&wp.element.createElement(F,{label:h("Hide Custom menu","podcast-player"),checked:!!Z,onChange:function(e){return $e({hideSubscribe:e})}}),wp.element.createElement(F,{label:h("Hide Podcast Search","podcast-player"),checked:!!ee,onChange:function(e){return $e({hideSearch:e})}}),wp.element.createElement(F,{label:h("Hide Episode Author/Podcaster Name","podcast-player"),checked:!!te,onChange:function(e){return $e({hideAuthor:e})}}),"feed"===N&&wp.element.createElement(F,{label:h("Hide Episode Text Content/Transcript","podcast-player"),checked:!!ae,onChange:function(e){return $e({hideContent:e})}}),wp.element.createElement(F,{label:h("Hide Load More Episodes Button","podcast-player"),checked:!!ne,onChange:function(e){return $e({hideLoadmore:e})}}),wp.element.createElement(F,{label:h("Hide Episode Download Link","podcast-player"),checked:!!le,onChange:function(e){return $e({hideDownload:e})}}),wp.element.createElement(F,{label:h("Hide Social Share Links","podcast-player"),checked:!!re,onChange:function(e){return $e({hideSocial:e})}}),wp.element.createElement(F,{label:h("Hide Episodes Featured Image","podcast-player"),checked:!!ie,onChange:function(e){return $e({hideFeatured:e})}})),wp.element.createElement(P,{initialOpen:!1,title:h("Podcast Player Styling","podcast-player")},wp.element.createElement(k,{label:h("Podcast Player Display Style","podcast-player"),value:S,onChange:function(e){return $e({displayStyle:e})},options:Ke}),Xe(S,"thumbnail")&&wp.element.createElement(k,{label:h("Thumbnail Cropping","podcast-player"),value:E,onChange:function(e){return $e({aspectRatio:e})},options:Ze}),Xe(S,"thumbnail")&&E&&wp.element.createElement(k,{label:h("Thumbnail Cropping Position","podcast-player"),value:O,onChange:function(e){return $e({cropMethod:e})},options:et}),Xe(S,"grid")&&wp.element.createElement(_,{label:h("Grid Columns","podcast-player"),value:U,onChange:function(e){return $e({gridColumns:e})},min:1,max:6}),!!this.isPremium&&wp.element.createElement(k,{label:h("Select Font Family","podcast-player"),value:be,options:We,onChange:function(e){return $e({fontFamily:e})}}),!!this.isPremium&&Xe(S,"txtcolor")&&wp.element.createElement(k,{label:h("Text Color Scheme","podcast-player"),value:ye,options:[{value:"",label:h("Dark Text","podcast-player")},{value:"ltext",label:h("Light Text","podcast-player")}],onChange:function(e){return $e({txtColor:e})}})),wp.element.createElement(w,{title:h("Podcast Player Color Scheme","podcast-player"),initialOpen:!1,colorSettings:[{value:g,onChange:function(e){return $e({accentColor:e})},label:h("Accent Color","podcast-player")}].concat(o(this.isPremium&&Xe(S,"bgcolor")?[{value:he,onChange:function(e){return $e({bgColor:e})},label:h("Player Background Color","podcast-player")}]:[]))}),"link"!==N&&wp.element.createElement(P,{initialOpen:!1,title:h("Sort & Filter Options","podcast-player")},wp.element.createElement(k,{label:h("Sort Podcast Episodes By","podcast-player"),value:r,onChange:function(e){return $e({sortBy:e})},options:[{value:"sort_title_desc",label:h("Title Descending","podcast-player")},{value:"sort_title_asc",label:h("Title Ascending","podcast-player")},{value:"sort_date_desc",label:h("Date Descending","podcast-player")},{value:"sort_date_asc",label:h("Date Ascending","podcast-player")},{value:"no_sort",label:h("Do Not Sort","podcast-player")},{value:"reverse_sort",label:h("Reverse Sort","podcast-player")}]}),wp.element.createElement(A,{label:h("Show episodes only if title contains following","podcast-player"),value:i,onChange:function(e){return $e({filterBy:e})}}),1<Ge.length&&"feed"===N&&wp.element.createElement(n,{listItems:Ge,selected:Ie,onItemChange:function(e){var t=Ie.indexOf(e);$e(-1===t?""===e?{slist:[e]}:{slist:[].concat(o(Ie),[e])}:""===e?{slist:[]}:{slist:Ie.filter((function(t){return t!==e}))})},label:h("Select Seasons to be displayed","podcast-player")}),1<Ve.length&&"feed"===N&&wp.element.createElement(n,{listItems:Ve,selected:Ue,onItemChange:function(e){var t=Ue.indexOf(e);$e(-1===t?""===e?{catlist:[e]}:{catlist:[].concat(o(Ue),[e])}:""===e?{catlist:[]}:{catlist:Ue.filter((function(t){return t!==e}))})},label:h("Select Categories to be displayed","podcast-player")}),!!Qe.length&&"link"!==N&&wp.element.createElement(n,{listItems:Qe,selected:Be,onItemChange:function(e){var t=Be.indexOf(e);$e(-1===t?""===e?{elist:[e]}:{elist:[].concat(o(Be),[e])}:""===e?{elist:[]}:{elist:Be.filter((function(t){return t!==e}))})},label:h("Select Episodes to be displayed","podcast-player")}),!!Qe.length&&"link"!==N&&!!Be.filter(Boolean).length&&wp.element.createElement(k,{label:h("Show or Hide above selected episodes","podcast-player"),value:Ne,onChange:function(e){return $e({edisplay:e})},options:[{value:"",label:h("Show above selected episodes","podcast-player")},{value:"hide",label:h("Hide above selected episodes","podcast-player")}]})),!!this.isPremium&&wp.element.createElement(P,{initialOpen:!1,title:h("Custom Audio Message","podcast-player")},wp.element.createElement(A,{label:h("Enter URL of mp3 audio file to be played","podcast-player"),value:se,onChange:function(e){return $e({audioMsg:e})}}),wp.element.createElement(_,{label:h("Replay Frequency","podcast-player"),help:h("After how many episodes the audio should be replayed","podcast-player"),value:pe,onChange:function(e){return $e({playFreq:e})},min:0,max:100}),wp.element.createElement(k,{label:h("When to start playing the audio message","podcast-player"),value:ue,onChange:function(e){return $e({msgStart:e})},options:[{value:"start",label:h("Start of the Episode","podcast-player")},{value:"end",label:h("End of the Episode","podcast-player")},{value:"custom",label:h("Custom Time","podcast-player")}]}),ue&&"custom"===ue&&wp.element.createElement("div",{className:"components-base-control"},wp.element.createElement("label",{className:"components-base-control__label"},h("Start playing audio at (time in hh:mm:ss)")),wp.element.createElement("div",{className:"components-datetime__time-field components-datetime__time-field-time"},wp.element.createElement("input",{className:"components-datetime__time-field-hours-input",type:"number",step:1,min:0,max:10,value:de[0],onChange:function(e){var t=e.target.value,a=de[1]?de[1]:0,n=de[2]?de[2]:0;$e({msgTime:[t,a,n]})}}),wp.element.createElement("span",{className:"components-datetime__time-separator","aria-hidden":"true"},":"),wp.element.createElement("input",{className:"components-datetime__time-field-hours-input",type:"number",step:1,min:0,max:59,value:de[1],onChange:function(e){var t=e.target.value,a=de[0]?de[0]:0,n=de[2]?de[2]:0;$e({msgTime:[a,t,n]})}}),wp.element.createElement("span",{className:"components-datetime__time-separator","aria-hidden":"true"},":"),wp.element.createElement("input",{className:"components-datetime__time-field-hours-input",type:"number",step:1,min:0,max:59,value:de[2],onChange:function(e){var t=e.target.value,a=de[0]?de[0]:0,n=de[1]?de[1]:0;$e({msgTime:[a,n,t]})}}))),wp.element.createElement(A,{label:h("Message to be displayed while playing audio.","podcast-player"),value:me,onChange:function(e){return $e({msgText:e})}}))),wp.element.createElement(x,null,wp.element.createElement(M,{block:"podcast-player/podcast-player",attributes:this.props.attributes})))}}])&&s(a.prototype,l),Object.defineProperty(a,"prototype",{writable:!1}),g}(b),N=U,j=wp.i18n.__;(0,wp.blocks.registerBlockType)("podcast-player/podcast-player",{title:j("Podcast Player","podcast-player"),description:j("Host your podcast anywhere, display them only using podcasting feed url.","podcast-player"),icon:wp.element.createElement("svg",{xmlns:"http://www.w3.org/2000/svg",width:"32",height:"32",viewBox:"0 0 32 32"},wp.element.createElement("path",{d:"M32 16c0-8.837-7.163-16-16-16s-16 7.163-16 16c0 6.877 4.339 12.739 10.428 15.002l-0.428 0.998h12l-0.428-0.998c6.089-2.263 10.428-8.125 10.428-15.002zM15.212 19.838c-0.713-0.306-1.212-1.014-1.212-1.838 0-1.105 0.895-2 2-2s2 0.895 2 2c0 0.825-0.499 1.533-1.212 1.839l-0.788-1.839-0.788 1.838zM16.821 19.915c1.815-0.379 3.179-1.988 3.179-3.915 0-2.209-1.791-4-4-4s-4 1.791-4 4c0 1.928 1.364 3.535 3.18 3.913l-2.332 5.441c-2.851-1.223-4.848-4.056-4.848-7.355 0-4.418 3.582-8.375 8-8.375s8 3.957 8 8.375c0 3.299-1.997 6.131-4.848 7.355l-2.331-5.439zM21.514 30.866l-2.31-5.39c3.951-1.336 6.796-5.073 6.796-9.476 0-5.523-4.477-10-10-10s-10 4.477-10 10c0 4.402 2.845 8.14 6.796 9.476l-2.31 5.39c-4.987-2.14-8.481-7.095-8.481-12.866 0-7.729 6.266-14.37 13.995-14.37s13.995 6.641 13.995 14.37c0 5.771-3.494 10.726-8.481 12.866z"})),category:"widgets",keywords:[j("Podcast","featured-content"),j("Feed to Audio","featured-content"),j("Podcasting","featured-content")],edit:N,save:function(){return null}})}();