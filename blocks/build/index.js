(window.webpackJsonp_lwtv_blocks=window.webpackJsonp_lwtv_blocks||[]).push([[1],[,,,function(e,t,a){},,function(e,t,a){},function(e,t,a){},function(e,t,a){},function(e,t,a){}]]),function(e){function t(t){for(var l,c,o=t[0],s=t[1],i=t[2],u=0,b=[];u<o.length;u++)c=o[u],Object.prototype.hasOwnProperty.call(r,c)&&r[c]&&b.push(r[c][0]),r[c]=0;for(l in s)Object.prototype.hasOwnProperty.call(s,l)&&(e[l]=s[l]);for(m&&m(t);b.length;)b.shift()();return n.push.apply(n,i||[]),a()}function a(){for(var e,t=0;t<n.length;t++){for(var a=n[t],l=!0,o=1;o<a.length;o++){var s=a[o];0!==r[s]&&(l=!1)}l&&(n.splice(t--,1),e=c(c.s=a[0]))}return e}var l={},r={0:0},n=[];function c(t){if(l[t])return l[t].exports;var a=l[t]={i:t,l:!1,exports:{}};return e[t].call(a.exports,a,a.exports,c),a.l=!0,a.exports}c.m=e,c.c=l,c.d=function(e,t,a){c.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:a})},c.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},c.t=function(e,t){if(1&t&&(e=c(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var a=Object.create(null);if(c.r(a),Object.defineProperty(a,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var l in e)c.d(a,l,function(t){return e[t]}.bind(null,l));return a},c.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return c.d(t,"a",t),t},c.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},c.p="";var o=window.webpackJsonp_lwtv_blocks=window.webpackJsonp_lwtv_blocks||[],s=o.push.bind(o);o.push=t,o=o.slice();for(var i=0;i<o.length;i++)t(o[i]);var m=s;n.push([9,1]),a()}([function(e,t){e.exports=window.wp.element},function(e,t){e.exports=window.wp.serverSideRender},function(e,t){e.exports=window.wp.blocks},,function(e,t,a){var l;!function(){"use strict";var a={}.hasOwnProperty;function r(){for(var e=[],t=0;t<arguments.length;t++){var l=arguments[t];if(l){var n=typeof l;if("string"===n||"number"===n)e.push(l);else if(Array.isArray(l)){if(l.length){var c=r.apply(null,l);c&&e.push(c)}}else if("object"===n)if(l.toString===Object.prototype.toString)for(var o in l)a.call(l,o)&&l[o]&&e.push(o);else e.push(l.toString())}}return e.join(" ")}e.exports?(r.default=r,e.exports=r):void 0===(l=function(){return r}.apply(t,[]))||(e.exports=l)}()},,,,,function(e,t,a){"use strict";a.r(t),a(3);var l=a(0),r=a(2);a(4),a(5);const{Fragment:n}=wp.element,{InnerBlocks:c,InspectorControls:o}=wp.blockEditor,{PanelBody:s,Button:i}=wp.components,{dispatch:m}=wp.data;Object(r.registerBlockType)("lwtv/affiliate-grid",{title:"Affiliate Grid",icon:Object(l.createElement)("svg",{"aria-hidden":"true",focusable:"false","data-prefix":"fas","data-icon":"grip-vertical",class:"svg-inline--fa fa-grip-vertical fa-w-10",role:"img",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 320 512"},Object(l.createElement)("path",{fill:"currentColor",d:"M96 32H32C14.33 32 0 46.33 0 64v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32V64c0-17.67-14.33-32-32-32zm0 160H32c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zm0 160H32c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zM288 32h-64c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32V64c0-17.67-14.33-32-32-32zm0 160h-64c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32zm0 160h-64c-17.67 0-32 14.33-32 32v64c0 17.67 14.33 32 32 32h64c17.67 0 32-14.33 32-32v-64c0-17.67-14.33-32-32-32z"})),category:"lezwatch",keywords:["affiliates"],className:!0,description:"A block for showing a grid of all affiliates.",edit:e=>{const{attributes:{placeholder:t},className:a,setAttributes:r,clientId:o}=e;return Object(l.createElement)(n,null,Object(l.createElement)("div",{className:a+" affiliate-grid row row-cols-1 row-cols-md-2 affiliate-items"},Object(l.createElement)(c,{template:[["lwtv/affiliate-item"]],allowedBlocks:[["lwtv/affiliate-item"]],defaultBlock:"lwtv/affiliate-item"})))},save:e=>{const{attributes:{className:t}}=e;return Object(l.createElement)("div",{className:t+" affiliate-grid row row-cols-1 row-cols-md-2 affiliate-items"},Object(l.createElement)(c.Content,null))}});const{Fragment:u}=wp.element,{InspectorControls:b,MediaUpload:p,MediaUploadCheck:d,MediaPlaceholder:g,RichText:v}=wp.blockEditor,{PanelBody:w,Button:h,ResponsiveWrapper:f,TextControl:O}=wp.components,{withSelect:j}=wp.data;Object(r.registerBlockType)("lwtv/affiliate-item",{title:"Affiliate Grid Item",category:"lezwatch",parent:["lwtv/affiliate-grid"],icon:"editor-rtl",category:"layout",className:!0,attributes:{name:{type:"string",default:"Example Affiliate"},url:{type:"string"},descr:{type:"string",default:"We are cool! Shop here!"},imgUrl:{type:"string",default:js_data.affiliate_default_image_url}},description:"An individual affiliate.",edit(e){let{attributes:t,setAttributes:a,isSelected:r,className:n}=e;const{name:c,url:o,descr:s,imgUrl:i}=t;return Object(l.createElement)(u,null,Object(l.createElement)(b,null,Object(l.createElement)(w,{title:"Affiliate Item Settings"},Object(l.createElement)(O,{label:"Affiliate Link",help:"Link to affiliate network (with any variables needed)",onChange:e=>a({url:e}),value:o}))),Object(l.createElement)("div",{className:n+" col mb-4"},Object(l.createElement)("div",{class:"card"},Object(l.createElement)("div",{class:"card-body"},Object(l.createElement)(p,{onSelect:function(e){console.log(e),a({imgUrl:e.sizes.full.url})},render:e=>{let{open:t}=e;return Object(l.createElement)("img",{src:i,onClick:t})}}),Object(l.createElement)(v,{tagName:"h5",className:"card-title",value:c,onChange:e=>a({name:e})}),Object(l.createElement)(v,{tagName:"p",className:"card-text",value:s,onChange:e=>a({descr:e})})))))},save(e){let{attributes:t,className:a}=e;const{name:r,url:n,descr:c,imgUrl:o}=t;let s=Object(l.createElement)("img",{src:o,class:"card-img-top",alt:r}),i="";return n&&(s=Object(l.createElement)("a",{href:n,target:"_new",rel:"noopener"},Object(l.createElement)("img",{src:o,class:"card-img-top",alt:r})),i=Object(l.createElement)("a",{href:n,target:"_new",class:"btn btn-primary",rel:"noopener"},"Shop ",r)),Object(l.createElement)("div",{className:a+" col mb-4"},Object(l.createElement)("div",{class:"card"},s,Object(l.createElement)("div",{class:"card-body"},Object(l.createElement)("h5",{class:"card-title"},r),Object(l.createElement)("p",{class:"card-text"},c),i)))}}),a(6);var E=a(1),y=a.n(E);const{Component:k,Fragment:C}=wp.element,{__:__}=wp.i18n,{SelectControl:x,PanelBody:S,TextControl:N}=wp.components,{InspectorControls:A}=wp.blockEditor,{withSelect:B}=wp.data;const{registerBlockType:z}=wp.blocks,{createElement:M,Fragment:T}=wp.element,{InspectorControls:_}=wp.blockEditor,{ServerSideRender:H,TextControl:L,PanelBody:V,SelectControl:P}=wp.components;z("lwtv/author-box",{title:"Team Member",icon:M("svg",{"aria-hidden":"true","data-prefix":"fas","data-icon":"portrait",role:"img",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 384 512",class:"svg-inline--fa fa-portrait fa-w-12 fa-3x"},M("path",{fill:"currentColor",d:"M336 0H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48zM192 128c35.3 0 64 28.7 64 64s-28.7 64-64 64-64-28.7-64-64 28.7-64 64-64zm112 236.8c0 10.6-10 19.2-22.4 19.2H102.4C90 384 80 375.4 80 364.8v-19.2c0-31.8 30.1-57.6 67.2-57.6h5c12.3 5.1 25.7 8 39.8 8s27.6-2.9 39.8-8h5c37.1 0 67.2 25.8 67.2 57.6v19.2z",class:""})),category:"lezwatch",className:!1,attributes:{users:{type:"string"},format:{type:"string",default:"large"}},edit:class extends k{render(){const{attributes:e,setAttributes:t,authors:a}=this.props,{users:r,format:n}=e,c=Object(l.createElement)(A,null,Object(l.createElement)(S,{title:"Team Member Settings"},Object(l.createElement)(N,{label:"Username",help:"Username or ID of team member (i.e. liljimmi, ipstenu, saralance)",value:r,onChange:e=>t({users:e})}),Object(l.createElement)(x,{label:"Card Format",type:"string",value:n,options:[{label:"Large",value:"large"},{label:"Compact",value:"compact"},{label:"Thumbnail",value:"thumbnail"}],onChange:e=>t({format:e})})));return Object(l.createElement)(C,null,c,Object(l.createElement)(y.a,{block:"lwtv/author-box",attributes:e}))}},save:()=>null});const{registerBlockType:Z}=wp.blocks,{createElement:I,Fragment:F}=wp.element,{InspectorControls:R}=wp.editor,{TextControl:q,PanelBody:G,SelectControl:D}=wp.components;Z("lez-library/glossary",{title:"Glossary",icon:I("svg",{"aria-hidden":"true","data-prefix":"fas","data-icon":"boxes",role:"img",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 576 512",class:"svg-inline--fa fa-boxes fa-w-18 fa-2x"},I("path",{fill:"currentColor",d:"M560 288h-80v96l-32-21.3-32 21.3v-96h-80c-8.8 0-16 7.2-16 16v192c0 8.8 7.2 16 16 16h224c8.8 0 16-7.2 16-16V304c0-8.8-7.2-16-16-16zm-384-64h224c8.8 0 16-7.2 16-16V16c0-8.8-7.2-16-16-16h-80v96l-32-21.3L256 96V0h-80c-8.8 0-16 7.2-16 16v192c0 8.8 7.2 16 16 16zm64 64h-80v96l-32-21.3L96 384v-96H16c-8.8 0-16 7.2-16 16v192c0 8.8 7.2 16 16 16h224c8.8 0 16-7.2 16-16V304c0-8.8-7.2-16-16-16z",class:""})),category:"lezwatch",className:!1,attributes:{taxonomy:{type:"string"}},edit:e=>{const{attributes:{placeholder:t},setAttributes:a}=e;return I(F,null,I(R,null,I(G,{title:"Glossary Block Settings"},I(D,{label:"Taxonomy",value:e.attributes.taxonomy,options:[{label:"Choose a taxonomy...",value:null},{label:"Clichés",value:"lez_cliches"},{label:"Tropes",value:"lez_tropes"},{label:"Formats",value:"lez_formats"},{label:"Genres",value:"lez_genres"},{label:"Intersections",value:"lez_intersections"}],onChange:t=>e.setAttributes({taxonomy:t})}))),I(y.a,{block:"lez-library/glossary",attributes:e.attributes}))},save:()=>null}),a(7);const{__:U}=wp.i18n,{Fragment:W,Component:J}=wp.element,{createBlock:Q,registerBlockType:Y}=wp.blocks,{RichText:K,PlainText:X,InspectorControls:$}=wp.blockEditor,{PanelBody:ee,ToggleControl:te,SelectControl:ae}=wp.components;Y("lwtv/grade",{apiVersion:2,title:U("Grade"),icon:Object(l.createElement)("svg",{"aria-hidden":"true","data-prefix":"far","data-icon":"star-exclamation",role:"img",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 576 512",class:"svg-inline--fa fa-star-exclamation fa-w-18 fa-3x"},Object(l.createElement)("path",{fill:"currentColor",d:"M252.5 184.6c-.4-4.6 3.3-8.6 8-8.6h55.1c4.7 0 8.3 4 8 8.6l-6.8 88c-.3 4.2-3.8 7.4-8 7.4h-41.5c-4.2 0-7.7-3.2-8-7.4l-6.8-88zM288 296c-22.1 0-40 17.9-40 40s17.9 40 40 40 40-17.9 40-40-17.9-40-40-40zm257.9-70L440.1 329l25 145.5c4.5 26.2-23.1 46-46.4 33.7L288 439.6l-130.7 68.7c-23.4 12.3-50.9-7.6-46.4-33.7l25-145.5L30.1 226c-19-18.5-8.5-50.8 17.7-54.6L194 150.2l65.3-132.4c11.8-23.8 45.7-23.7 57.4 0L382 150.2l146.1 21.2c26.2 3.8 36.7 36.1 17.8 54.6zm-56.8-11.7l-139-20.2-62.1-126L225.8 194l-139 20.2 100.6 98-23.7 138.4L288 385.3l124.3 65.4-23.7-138.4 100.5-98z",class:""})),category:"lezwatch",keywords:[U("grade"),U("review")],customClassName:!1,className:!0,attributes:{summary:{type:"string"},title:{type:"string"},grade:{type:"string",default:"C"},show:{type:"number",default:0}},edit:e=>{const{attributes:{placeholder:t},className:a,setAttributes:r}=e,{summary:n,grade:c}=e.attributes;return Object(l.createElement)(W,null,Object(l.createElement)($,null,Object(l.createElement)(ee,{title:"Grade Block Settings"},Object(l.createElement)(ae,{label:"Grade",value:c,options:[{label:"Pick a grade...",value:null},{label:"A+",value:"A+"},{label:"A",value:"A"},{label:"A-",value:"A-"},{label:"B+",value:"B+"},{label:"B",value:"B"},{label:"B-",value:"B-"},{label:"C+",value:"C+"},{label:"C",value:"C"},{label:"C-",value:"C-"},{label:"D",value:"D"},{label:"F",value:"F"}],onChange:t=>e.setAttributes({grade:t})}))),Object(l.createElement)("div",{className:a+" bd-callout show-grade"},Object(l.createElement)("div",{class:"show-grade grade"},c),Object(l.createElement)("div",{class:"show-grade body"},Object(l.createElement)(K,{tagName:"p",value:n,placeholder:"Summary (could have been better...)",onChange:e=>r({summary:e})}))))},save:e=>{const{attributes:{className:t},setAttributes:a}=e,{summary:r,grade:n}=e.attributes;return Object(l.createElement)(W,null,Object(l.createElement)("div",{className:t+" bd-callout show-grade"},Object(l.createElement)("div",{class:"grade alert alert-info"},n),Object(l.createElement)("div",{class:"show-grade body"},Object(l.createElement)(K.Content,{tagName:"p",value:r}))))}}),a(8);const{__:le}=wp.i18n,{Fragment:re}=wp.element,{createBlock:ne,registerBlockType:ce}=wp.blocks,{RichText:oe,PlainText:se}=wp.blockEditor,{PanelBody:ie,ToggleControl:me,RangeControl:ue,SelectControl:be}=wp.components;function pe(e){let{score:t}=e;return Object(l.createElement)(re,null,Object(l.createElement)("span",{"data-toggle":"tooltip","aria-label":"How good is this show for queers?",title:"","data-original-title":"How good is this show for queers?"},Object(l.createElement)("button",{type:"button",class:"btn btn-dark"},"Queer Score: ",""+t)))}function de(e){let t,a,{score:r}=e,n="info",c="meh";switch(r){case"yes":n="success",c="thumbs-up",t="M3,9a1,1,0,0,0-1,1V21a1,1,0,0,0,2,0V10A1,1,0,0,0,3,9ZM20,9H12.37l1.48-3.89A2.35,2.35,0,0,0,13,2.38,2.06,2.06,0,0,0,10.11,3L6,9H6v9a4,4,0,0,0,4,4h6.7a2,2,0,0,0,1.83-1.19l3.3-7.42a2.06,2.06,0,0,0,.17-.81V11A2,2,0,0,0,20,9Z";break;case"no":n="danger",c="thumbs-down",t="M14,2H7.3A2,2,0,0,0,5.47,3.19l-3.3,7.42a2.06,2.06,0,0,0-.17.81V13a2,2,0,0,0,2,2h7.63l-1.48,3.89A2.35,2.35,0,0,0,11,21.62a2.06,2.06,0,0,0,2.93-.57L18,15h0V6A4,4,0,0,0,14,2Zm7,0a1,1,0,0,0-1,1V14a1,1,0,0,0,2,0V3A1,1,0,0,0,21,2Z";break;case"tbd":n="info",c="clock-icon",t="M12,5a1,1,0,0,0-1,1V8H9a1,1,0,0,0,0,2h4V6A1,1,0,0,0,12,5ZM23,22H1a1,1,0,1,0,0,2H23a1,1,0,0,0,0-2ZM21,9a9,9,0,1,0-18,0L3,20H21Zm-9,7a7,7,0,1,1,7-7A7,7,0,0,1,12,16Z";break;default:t="M12,0A12,12,0,1,0,24,12,12,12,0,0,0,12,0ZM7.5,8A1.5,1.5,0,1,1,6,9.5,1.5,1.5,0,0,1,7.5,8ZM17,17H7a1,1,0,0,1,0-2H17a1,1,0,0,1,0,2Zm-.5-6A1.5,1.5,0,1,1,18,9.5,1.5,1.5,0,0,1,16.5,11Z"}return a=Object(l.createElement)("title",null,c),Object(l.createElement)(re,null,Object(l.createElement)("span",{"data-toggle":"tooltip","aria-label":"Is this show worth watching? "+r,title:"","data-original-title":"Is this show worth watching? "+r},Object(l.createElement)("button",{type:"button",className:"btn btn-"+n},"Worth It? ",Object(l.createElement)("span",{role:"img",className:"screener screener-worthit "+r},Object(l.createElement)("span",{class:"symbolicon",role:"img"},Object(l.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",id:c},a,Object(l.createElement)("path",{d:t})))))))}function ge(e){let t,{score:a}=e;switch(a){case"high":t="danger";break;case"medium":t="warning";break;default:t="info"}if("none"!==a)return Object(l.createElement)(re,null,Object(l.createElement)("span",{"data-toggle":"tooltip","aria-label":"Warning - This show contains triggers",title:"Warning - This show contains triggers"},Object(l.createElement)("button",{type:"button",className:"btn btn-"+t},Object(l.createElement)("span",{role:"img",className:"screener screener-warn "+t},Object(l.createElement)("span",{class:"symbolicon",role:"img"},Object(l.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},Object(l.createElement)("title",null,"warning"),Object(l.createElement)("g",{id:"warning"},Object(l.createElement)("path",{d:"M23.51,17.5,15.18,2.85a3.66,3.66,0,0,0-6.36,0L.49,17.5A3.68,3.68,0,0,0,3.67,23H20.33A3.68,3.68,0,0,0,23.51,17.5ZM11,7a1,1,0,0,1,1-1h0a1,1,0,0,1,1,1v7a1,1,0,0,1-1,1h0a1,1,0,0,1-1-1Zm1,13a1.5,1.5,0,1,1,1.5-1.5A1.5,1.5,0,0,1,12,20Z"}))))))))}function ve(e){let t,{score:a}=e;switch(a){case"anti":case"bronze":t="danger";break;case"silver":t="warning";break;default:t="gold"}if("none"!==a)return Object(l.createElement)(re,null,Object(l.createElement)("span",{"data-toggle":"tooltip","aria-label":a+" Star Show",title:"","data-original-title":a+" Star Show"},Object(l.createElement)("button",{type:"button",class:"btn btn-info"},Object(l.createElement)("span",{role:"img",className:"screener screener-star "+t},Object(l.createElement)("span",{class:"symbolicon",role:"img"},Object(l.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},Object(l.createElement)("title",null,"star"),Object(l.createElement)("g",{id:"star"},Object(l.createElement)("path",{d:"M24,9.69A1,1,0,0,0,23,9H15.39L13,1.68a1,1,0,0,0-1.9,0L8.61,9H1a1,1,0,0,0-.95.69,1,1,0,0,0,.36,1.12l6.12,4.45L4.05,22.68a1,1,0,0,0,.36,1.12,1,1,0,0,0,1.17,0L12,19.23l6.42,4.58A1,1,0,0,0,19,24a1,1,0,0,0,.59-.2A1,1,0,0,0,20,22.68l-2.48-7.42,6.12-4.45A1,1,0,0,0,24,9.69Z"}))))))))}ce("lwtv/screener",{title:le("Screener Reviews"),icon:"video-alt",category:"lezwatch",keywords:[le("screener"),le("review")],customClassName:!1,className:!1,attributes:{title:{type:"string"},summary:{type:"string"},queer:{type:"string",default:"3"},worthit:{type:"string",default:"meh"},star:{type:"string",default:"none"},trigger:{type:"string",default:"none"}},edit:e=>{const{attributes:{placeholder:t},className:a,setAttributes:r}=e,{title:n,summary:c,queer:o,worthit:s,star:i,trigger:m}=e.attributes;function u(e){const t=e.target.querySelector("option:checked");r({queer:t.value}),e.preventDefault()}function b(e){const t=e.target.querySelector("option:checked");r({worthit:t.value}),e.preventDefault()}function p(e){const t=e.target.querySelector("option:checked");r({star:t.value}),e.preventDefault()}function d(e){const t=e.target.querySelector("option:checked");r({trigger:t.value}),e.preventDefault()}return Object(l.createElement)(re,null,Object(l.createElement)("div",{className:a+" bd-callout screener-shortcode"},Object(l.createElement)("h5",null,"Screener Review On ",Object(l.createElement)(se,{tagName:"em",value:n,placeholder:"Show Title",onChange:e=>r({title:e})})),Object(l.createElement)(oe,{tagName:"p",value:c,placeholder:"Content of Review",onChange:e=>r({summary:e})}),Object(l.createElement)("p",null,Object(l.createElement)("span",null,Object(l.createElement)("button",{type:"button",class:"btn btn-dark"},"Queer:",Object(l.createElement)("form",{onSubmit:u},Object(l.createElement)("select",{value:o,onChange:u},Object(l.createElement)("option",{value:"0"},"0"),Object(l.createElement)("option",{value:"1"},"1"),Object(l.createElement)("option",{value:"2"},"2"),Object(l.createElement)("option",{value:"3"},"3"),Object(l.createElement)("option",{value:"4"},"4"),Object(l.createElement)("option",{value:"5"},"5"))))),Object(l.createElement)("span",null,Object(l.createElement)("button",{type:"button",className:"btn btn-"+s},"Worth:",Object(l.createElement)("form",{onSubmit:b},Object(l.createElement)("select",{value:s,onChange:b},Object(l.createElement)("option",{value:"yes"},"Yes"),Object(l.createElement)("option",{value:"meh"},"Meh"),Object(l.createElement)("option",{value:"no"},"No"),Object(l.createElement)("option",{value:"tbd"},"TBD"))))),Object(l.createElement)("span",null,Object(l.createElement)("button",{type:"button",className:"btn btn-"+m},"Trigger:",Object(l.createElement)("form",{onSubmit:d},Object(l.createElement)("select",{value:m,onChange:d},Object(l.createElement)("option",{value:"none"},"None"),Object(l.createElement)("option",{value:"low"},"Low"),Object(l.createElement)("option",{value:"medium"},"Medium"),Object(l.createElement)("option",{value:"high"},"High"))))),Object(l.createElement)("span",null,Object(l.createElement)("button",{type:"button",className:"btn btn-"+i},"Star:",Object(l.createElement)("form",{onSubmit:p},Object(l.createElement)("select",{value:i,onChange:p},Object(l.createElement)("option",{value:"none"},"None"),Object(l.createElement)("option",{value:"gold"},"Gold"),Object(l.createElement)("option",{value:"silver"},"Silver"),Object(l.createElement)("option",{value:"bronze"},"Bronze"),Object(l.createElement)("option",{value:"anti"},"Anti"))))))))},save:e=>{const{attributes:{className:t},setAttributes:a}=e,{title:r,summary:n,queer:c,worthit:o,star:s,trigger:i}=e.attributes;return Object(l.createElement)(re,null,Object(l.createElement)("div",{className:t+" bd-callout screener-shortcode"},Object(l.createElement)("h5",null,"Screener Review On ",Object(l.createElement)(oe.Content,{tagName:"em",value:r})),Object(l.createElement)(oe.Content,{tagName:"p",value:n}),Object(l.createElement)("p",null,Object(l.createElement)(pe,{score:c})," ",Object(l.createElement)(de,{score:o})," ",Object(l.createElement)(ge,{score:i})," ",Object(l.createElement)(ve,{score:s}))))}});const{Fragment:we}=wp.element,{registerBlockType:he}=wp.blocks;he("lwtv/tvshow-calendar",{title:"TV Shows Calendar",icon:Object(l.createElement)("svg",{"aria-hidden":"true",focusable:"false","data-prefix":"fas","data-icon":"calendar-week",role:"img",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 448 512",class:"svg-inline--fa fa-calendar-week fa-w-14 fa-3x"},Object(l.createElement)("path",{fill:"currentColor",d:"M0 464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V192H0v272zm64-192c0-8.8 7.2-16 16-16h288c8.8 0 16 7.2 16 16v64c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16v-64zM400 64h-48V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H160V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H48C21.5 64 0 85.5 0 112v48h448v-48c0-26.5-21.5-48-48-48z",class:""})),category:"lezwatch",keywords:["calendar","tv shows"],className:!1,edit:e=>Object(l.createElement)(y.a,{block:"lwtv/tvshow-calendar"}),save:()=>null})}]);