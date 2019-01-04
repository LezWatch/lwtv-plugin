!function(e){function t(l){if(n[l])return n[l].exports;var a=n[l]={i:l,l:!1,exports:{}};return e[l].call(a.exports,a,a.exports,t),a.l=!0,a.exports}var n={};t.m=e,t.c=n,t.d=function(e,n,l){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:l})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=0)}([function(e,t,n){"use strict";Object.defineProperty(t,"__esModule",{value:!0});n(1),n(5),n(7),n(10),n(13)},function(e,t,n){"use strict";var l=n(2),a=(n.n(l),n(3)),r=(n.n(a),n(4)),o=wp.blocks.registerBlockType,c=wp.element,s=(c.createElement,c.Fragment,wp.editor.InspectorControls,wp.components);s.ServerSideRender,s.TextControl,s.PanelBody,s.SelectControl;o("lwtv/author-box",{title:"Author Box",icon:wp.element.createElement("svg",{"aria-hidden":"true","data-prefix":"fas","data-icon":"portrait",role:"img",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 384 512",class:"svg-inline--fa fa-portrait fa-w-12 fa-3x"},wp.element.createElement("path",{fill:"currentColor",d:"M336 0H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48zM192 128c35.3 0 64 28.7 64 64s-28.7 64-64 64-64-28.7-64-64 28.7-64 64-64zm112 236.8c0 10.6-10 19.2-22.4 19.2H102.4C90 384 80 375.4 80 364.8v-19.2c0-31.8 30.1-57.6 67.2-57.6h5c12.3 5.1 25.7 8 39.8 8s27.6-2.9 39.8-8h5c37.1 0 67.2 25.8 67.2 57.6v19.2z",class:""})),category:"lezwatch",className:!1,attributes:{users:{type:"string"},format:{type:"string",default:"large"}},edit:r.a,save:function(){return null}})},function(e,t){},function(e,t){},function(e,t,n){"use strict";function l(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function a(e,t){if(!e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return!t||"object"!==typeof t&&"function"!==typeof t?e:t}function r(e,t){if("function"!==typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t)}var o=function(){function e(e,t){for(var n=0;n<t.length;n++){var l=t[n];l.enumerable=l.enumerable||!1,l.configurable=!0,"value"in l&&(l.writable=!0),Object.defineProperty(e,l.key,l)}}return function(t,n,l){return n&&e(t.prototype,n),l&&e(t,l),t}}(),c=wp.element,s=c.Component,i=c.Fragment,m=(wp.i18n.__,wp.components),u=m.SelectControl,p=m.PanelBody,w=m.ServerSideRender,v=wp.editor.InspectorControls,g=wp.data.withSelect,b=function(e){function t(){return l(this,t),a(this,(t.__proto__||Object.getPrototypeOf(t)).apply(this,arguments))}return r(t,e),o(t,[{key:"getAuthorsForSelect",value:function(){return this.props.authors.map(function(e){return{label:e.name,value:e.id}})}},{key:"render",value:function(){function e(e){return parseInt(e.id)===parseInt(r)}var t=this.props,n=t.attributes,l=t.setAttributes,a=t.authors,r=n.users,o=n.format,c=this.getAuthorsForSelect();c.push({label:"- Select User -",value:0}),c.sort(function(e,t){return e.value-t.value});var s=wp.element.createElement(v,null,wp.element.createElement(p,{title:"Author Profile Settings"},wp.element.createElement(u,{label:"Author ID",type:"number",value:r,options:c,onChange:function(e){return l({users:e})}}),wp.element.createElement(u,{label:"Format",type:"string",value:o,options:[{label:"Large",value:"large"},{label:"Compact",value:"compact"},{label:"Thumbnail",value:"thumbnail"}],onChange:function(e){return l({format:e})}})));a.find(e);return wp.element.createElement(i,null,s,wp.element.createElement(w,{block:"lwtv/author-box",attributes:n}))}}]),t}(s);t.a=g(function(e){return{authors:e("core").getAuthors()}})(b)},function(e,t,n){"use strict";var l=n(6),a=(n.n(l),wp.i18n.__,wp.blocks.registerBlockType),r=wp.element.Fragment,o=wp.components,c=o.ServerSideRender;o.DateTimePicker;a("lez-library/cpt-meta",{title:"LezWatch.TV Custom Post Type Meta",icon:wp.element.createElement("svg",{"aria-hidden":"true","data-prefix":"fas","data-icon":"alicorn",role:"img",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 640 512",class:"svg-inline--fa fa-alicorn fa-w-20 fa-3x"},wp.element.createElement("path",{fill:"currentColor",d:"M631.98 32H531.73c5.93-6.14 10.4-13.63 12.18-22.36 1.01-4.96-2.88-9.64-7.94-9.64H416c-70.69 0-128 57.31-128 128h-.08c-63.92 0-104.2-36.78-127.66-90.27-3.22-7.35-13.61-7.76-17.04-.5C133.49 57.82 128 80.8 128 105.1c0 67.04 51.01 133.09 128 147.74v3.17c-76.89 0-133.8-49.67-152.67-108.99-5.58 4.17-10.75 8.86-15.33 14.1V160c-48.53 0-88 39.47-88 88v56c0 8.84 7.16 16 16 16h16c8.84 0 16-7.16 16-16v-56c0-13.22 6.87-24.39 16.78-31.68-.21 2.58-.78 5.05-.78 7.68 0 27.64 11.84 52.36 30.54 69.88l-25.72 68.6a63.945 63.945 0 0 0-2.16 37.99l24.85 99.41A15.982 15.982 0 0 0 107.02 512h65.96c10.41 0 18.05-9.78 15.52-19.88l-26.31-105.26 23.84-63.59 102.04 22.33V496c0 8.84 7.16 16 16 16h64c8.84 0 16-7.16 16-16V318.22c19.74-20.19 32-47.75 32-78.22 0-.22-.07-.42-.08-.64V136.89l16 7.11 18.9 37.7c7.45 14.87 25.05 21.55 40.49 15.37l32.55-13.02a31.997 31.997 0 0 0 20.12-29.74L544 83.3l92.42-36.65c6.59-4.38 3.48-14.65-4.44-14.65zM480 96c-8.84 0-16-7.16-16-16s7.16-16 16-16 16 7.16 16 16-7.16 16-16 16z",class:""})),category:"lezwatch",description:"A programatically insertable block for actor, character, and show templates.",inserter:!1,attributes:{post_id:{type:"int"}},edit:function(e){e.ClassName,e.attributes,e.setAttributes;return e.attributes.post_id=jQuery("#post_ID").val(),wp.element.createElement(r,null,wp.element.createElement(c,{block:"lez-library/cpt-meta",attributes:e.attributes}))},save:function(e){return null}})},function(e,t){},function(e,t,n){"use strict";var l=n(8),a=(n.n(l),n(9)),r=(n.n(a),wp.i18n.__),o=wp.element,c=o.Fragment,s=(o.Component,wp.blocks),i=(s.createBlock,s.registerBlockType),m=wp.editor,u=m.RichText,p=(m.PlainText,m.InspectorControls),w=wp.components,v=w.PanelBody,g=(w.ToggleControl,w.SelectControl);i("lwtv/grade",{title:r("Grade"),icon:wp.element.createElement("svg",{"aria-hidden":"true","data-prefix":"far","data-icon":"star-exclamation",role:"img",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 576 512",class:"svg-inline--fa fa-star-exclamation fa-w-18 fa-3x"},wp.element.createElement("path",{fill:"currentColor",d:"M252.5 184.6c-.4-4.6 3.3-8.6 8-8.6h55.1c4.7 0 8.3 4 8 8.6l-6.8 88c-.3 4.2-3.8 7.4-8 7.4h-41.5c-4.2 0-7.7-3.2-8-7.4l-6.8-88zM288 296c-22.1 0-40 17.9-40 40s17.9 40 40 40 40-17.9 40-40-17.9-40-40-40zm257.9-70L440.1 329l25 145.5c4.5 26.2-23.1 46-46.4 33.7L288 439.6l-130.7 68.7c-23.4 12.3-50.9-7.6-46.4-33.7l25-145.5L30.1 226c-19-18.5-8.5-50.8 17.7-54.6L194 150.2l65.3-132.4c11.8-23.8 45.7-23.7 57.4 0L382 150.2l146.1 21.2c26.2 3.8 36.7 36.1 17.8 54.6zm-56.8-11.7l-139-20.2-62.1-126L225.8 194l-139 20.2 100.6 98-23.7 138.4L288 385.3l124.3 65.4-23.7-138.4 100.5-98z",class:""})),category:"lezwatch",keywords:[r("grade"),r("review")],customClassName:!1,className:!0,attributes:{summary:{type:"string"},title:{type:"string"},grade:{type:"string",default:"C"},show:{type:"number",default:0}},edit:function(e){var t=(e.attributes.placeholder,e.className),n=e.setAttributes,l=e.attributes,a=l.summary,r=l.grade;return wp.element.createElement(c,null,wp.element.createElement(p,null,wp.element.createElement(v,{title:"Grade Block Settings"},wp.element.createElement(g,{label:"Grade",value:r,options:[{label:"Pick a grade...",value:null},{label:"A+",value:"A+"},{label:"A",value:"A"},{label:"A-",value:"A-"},{label:"B+",value:"B+"},{label:"B",value:"B"},{label:"B-",value:"B+"},{label:"C+",value:"C+"},{label:"C",value:"C"},{label:"C-",value:"C-"},{label:"D",value:"D"},{label:"F",value:"F"}],onChange:function(t){return e.setAttributes({grade:t})}}))),wp.element.createElement("div",{className:t+" bd-callout show-grade"},wp.element.createElement("div",{class:"show-grade grade"},r),wp.element.createElement("div",{class:"show-grade body"},wp.element.createElement(u,{tagName:"p",value:a,placeholder:"Summary (could have been better...)",onChange:function(e){return n({summary:e})}}))))},save:function(e){var t=e.attributes.className,n=(e.setAttributes,e.attributes),l=n.summary,a=n.grade;return wp.element.createElement(c,null,wp.element.createElement("div",{className:t+" bd-callout show-grade"},wp.element.createElement("div",{class:"grade alert alert-info"},a),wp.element.createElement("div",{class:"show-grade body"},wp.element.createElement(u.Content,{tagName:"p",value:l}))))}})},function(e,t){},function(e,t){},function(e,t,n){"use strict";var l=n(11),a=(n.n(l),n(12)),r=(n.n(a),wp.blocks.registerBlockType),o=wp.element,c=(o.createElement,o.Fragment),s=wp.editor.InspectorControls,i=wp.components,m=i.ServerSideRender,u=(i.TextControl,i.PanelBody),p=i.SelectControl;r("lez-library/glossary",{title:"Glossary",icon:wp.element.createElement("svg",{"aria-hidden":"true","data-prefix":"fas","data-icon":"boxes",role:"img",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 576 512",class:"svg-inline--fa fa-boxes fa-w-18 fa-2x"},wp.element.createElement("path",{fill:"currentColor",d:"M560 288h-80v96l-32-21.3-32 21.3v-96h-80c-8.8 0-16 7.2-16 16v192c0 8.8 7.2 16 16 16h224c8.8 0 16-7.2 16-16V304c0-8.8-7.2-16-16-16zm-384-64h224c8.8 0 16-7.2 16-16V16c0-8.8-7.2-16-16-16h-80v96l-32-21.3L256 96V0h-80c-8.8 0-16 7.2-16 16v192c0 8.8 7.2 16 16 16zm64 64h-80v96l-32-21.3L96 384v-96H16c-8.8 0-16 7.2-16 16v192c0 8.8 7.2 16 16 16h224c8.8 0 16-7.2 16-16V304c0-8.8-7.2-16-16-16z",class:""})),category:"lezwatch",className:!1,attributes:{taxonomy:{type:"string"}},edit:function(e){e.attributes.placeholder,e.setAttributes;return wp.element.createElement(c,null,wp.element.createElement(s,null,wp.element.createElement(u,{title:"Glossary Block Settings"},wp.element.createElement(p,{label:"Taxonomy",value:e.attributes.taxonomy,options:[{label:"Choose a taxonomy...",value:null},{label:"Clich\xe9s",value:"lez_cliches"},{label:"Tropes",value:"lez_tropes"},{label:"Formats",value:"lez_formats"},{label:"Genres",value:"lez_genres"},{label:"Intersections",value:"lez_intersections"}],onChange:function(t){return e.setAttributes({taxonomy:t})}}))),wp.element.createElement(m,{block:"lez-library/glossary",attributes:e.attributes}))},save:function(){return null}})},function(e,t){},function(e,t){},function(e,t,n){"use strict";function l(e){var t=e.score;return wp.element.createElement(m,null,wp.element.createElement("span",{"data-toggle":"tooltip","aria-label":"How good is this show for queers?",title:"","data-original-title":"How good is this show for queers?"},wp.element.createElement("button",{type:"button",class:"btn btn-dark"},"Queer Score: ",""+t)))}function a(e){var t=e.score,n="info",l=void 0,a="meh",r=void 0;switch(t){case"yes":n="success",a="thumbs-up",l="M3,9a1,1,0,0,0-1,1V21a1,1,0,0,0,2,0V10A1,1,0,0,0,3,9ZM20,9H12.37l1.48-3.89A2.35,2.35,0,0,0,13,2.38,2.06,2.06,0,0,0,10.11,3L6,9H6v9a4,4,0,0,0,4,4h6.7a2,2,0,0,0,1.83-1.19l3.3-7.42a2.06,2.06,0,0,0,.17-.81V11A2,2,0,0,0,20,9Z";break;case"no":n="danger",a="thumbs-down",l="M14,2H7.3A2,2,0,0,0,5.47,3.19l-3.3,7.42a2.06,2.06,0,0,0-.17.81V13a2,2,0,0,0,2,2h7.63l-1.48,3.89A2.35,2.35,0,0,0,11,21.62a2.06,2.06,0,0,0,2.93-.57L18,15h0V6A4,4,0,0,0,14,2Zm7,0a1,1,0,0,0-1,1V14a1,1,0,0,0,2,0V3A1,1,0,0,0,21,2Z";break;case"tbd":n="info",a="clock-icon",l="M12,5a1,1,0,0,0-1,1V8H9a1,1,0,0,0,0,2h4V6A1,1,0,0,0,12,5ZM23,22H1a1,1,0,1,0,0,2H23a1,1,0,0,0,0-2ZM21,9a9,9,0,1,0-18,0L3,20H21Zm-9,7a7,7,0,1,1,7-7A7,7,0,0,1,12,16Z";break;default:l="M12,0A12,12,0,1,0,24,12,12,12,0,0,0,12,0ZM7.5,8A1.5,1.5,0,1,1,6,9.5,1.5,1.5,0,0,1,7.5,8ZM17,17H7a1,1,0,0,1,0-2H17a1,1,0,0,1,0,2Zm-.5-6A1.5,1.5,0,1,1,18,9.5,1.5,1.5,0,0,1,16.5,11Z"}return r=wp.element.createElement("title",null,a),wp.element.createElement(m,null,wp.element.createElement("span",{"data-toggle":"tooltip","aria-label":"Is this show worth watching? "+t,title:"","data-original-title":"Is this show worth watching? "+t},wp.element.createElement("button",{type:"button",className:"btn btn-"+n},"Worth It?\xa0",wp.element.createElement("span",{role:"img",className:"screener screener-worthit "+t},wp.element.createElement("span",{class:"symbolicon",role:"img"},wp.element.createElement("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",id:a},r,wp.element.createElement("path",{d:l})))))))}function r(e){var t=e.score,n=void 0;switch(t){case"high":n="danger";break;case"medium":n="warning";break;default:n="info"}if("none"!==t)return wp.element.createElement(m,null,wp.element.createElement("span",{"data-toggle":"tooltip","aria-label":"Warning - This show contains triggers",title:"Warning - This show contains triggers"},wp.element.createElement("button",{type:"button",className:"btn btn-"+n},wp.element.createElement("span",{role:"img",className:"screener screener-warn "+n},wp.element.createElement("span",{class:"symbolicon",role:"img"},wp.element.createElement("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},wp.element.createElement("title",null,"warning"),wp.element.createElement("g",{id:"warning"},wp.element.createElement("path",{d:"M23.51,17.5,15.18,2.85a3.66,3.66,0,0,0-6.36,0L.49,17.5A3.68,3.68,0,0,0,3.67,23H20.33A3.68,3.68,0,0,0,23.51,17.5ZM11,7a1,1,0,0,1,1-1h0a1,1,0,0,1,1,1v7a1,1,0,0,1-1,1h0a1,1,0,0,1-1-1Zm1,13a1.5,1.5,0,1,1,1.5-1.5A1.5,1.5,0,0,1,12,20Z"}))))))))}function o(e){var t=e.score,n=void 0;switch(t){case"anti":case"bronze":n="danger";break;case"silver":n="warning";break;default:n="gold"}if("none"!==t)return wp.element.createElement(m,null,wp.element.createElement("span",{"data-toggle":"tooltip","aria-label":t+" Star Show",title:"","data-original-title":t+" Star Show"},wp.element.createElement("button",{type:"button",class:"btn btn-info"},wp.element.createElement("span",{role:"img",className:"screener screener-star "+n},wp.element.createElement("span",{class:"symbolicon",role:"img"},wp.element.createElement("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},wp.element.createElement("title",null,"star"),wp.element.createElement("g",{id:"star"},wp.element.createElement("path",{d:"M24,9.69A1,1,0,0,0,23,9H15.39L13,1.68a1,1,0,0,0-1.9,0L8.61,9H1a1,1,0,0,0-.95.69,1,1,0,0,0,.36,1.12l6.12,4.45L4.05,22.68a1,1,0,0,0,.36,1.12,1,1,0,0,0,1.17,0L12,19.23l6.42,4.58A1,1,0,0,0,19,24a1,1,0,0,0,.59-.2A1,1,0,0,0,20,22.68l-2.48-7.42,6.12-4.45A1,1,0,0,0,24,9.69Z"}))))))))}var c=n(14),s=(n.n(c),n(15)),i=(n.n(s),wp.i18n.__),m=wp.element.Fragment,u=wp.blocks,p=(u.createBlock,u.registerBlockType),w=wp.editor,v=w.RichText,g=w.PlainText,b=wp.components;b.PanelBody,b.ToggleControl,b.RangeControl,b.SelectControl;p("lwtv/screener",{title:i("Screener Reviews"),icon:"video-alt",category:"lezwatch",keywords:[i("screener"),i("review")],customClassName:!1,className:!1,attributes:{title:{type:"string"},summary:{type:"string"},queer:{type:"string",default:"3"},worthit:{type:"string",default:"meh"},star:{type:"string",default:"none"},trigger:{type:"string",default:"none"}},edit:function(e){function t(e){var t=e.target.querySelector("option:checked");o({queer:t.value}),e.preventDefault()}function n(e){var t=e.target.querySelector("option:checked");o({worthit:t.value}),e.preventDefault()}function l(e){var t=e.target.querySelector("option:checked");o({star:t.value}),e.preventDefault()}function a(e){var t=e.target.querySelector("option:checked");o({trigger:t.value}),e.preventDefault()}var r=(e.attributes.placeholder,e.className),o=e.setAttributes,c=e.attributes,s=c.title,i=c.summary,u=c.queer,p=c.worthit,w=c.star,b=c.trigger;return wp.element.createElement(m,null,wp.element.createElement("div",{className:r+" bd-callout screener-shortcode"},wp.element.createElement("h5",null,"Screener Review On\xa0",wp.element.createElement(g,{tagName:"em",value:s,placeholder:"Show Title",onChange:function(e){return o({title:e})}})),wp.element.createElement(v,{tagName:"p",value:i,placeholder:"Content of Review",onChange:function(e){return o({summary:e})}}),wp.element.createElement("p",null,wp.element.createElement("span",null,wp.element.createElement("button",{type:"button",class:"btn btn-dark"},"Queer:",wp.element.createElement("form",{onSubmit:t},wp.element.createElement("select",{value:u,onChange:t},wp.element.createElement("option",{value:"0"},"0"),wp.element.createElement("option",{value:"1"},"1"),wp.element.createElement("option",{value:"2"},"2"),wp.element.createElement("option",{value:"3"},"3"),wp.element.createElement("option",{value:"4"},"4"),wp.element.createElement("option",{value:"5"},"5"))))),wp.element.createElement("span",null,wp.element.createElement("button",{type:"button",className:"btn btn-"+p},"Worth:",wp.element.createElement("form",{onSubmit:n},wp.element.createElement("select",{value:p,onChange:n},wp.element.createElement("option",{value:"yes"},"Yes"),wp.element.createElement("option",{value:"meh"},"Meh"),wp.element.createElement("option",{value:"no"},"No"),wp.element.createElement("option",{value:"tbd"},"TBD"))))),wp.element.createElement("span",null,wp.element.createElement("button",{type:"button",className:"btn btn-"+b},"Trigger:",wp.element.createElement("form",{onSubmit:a},wp.element.createElement("select",{value:b,onChange:a},wp.element.createElement("option",{value:"none"},"None"),wp.element.createElement("option",{value:"low"},"Low"),wp.element.createElement("option",{value:"medium"},"Medium"),wp.element.createElement("option",{value:"high"},"High"))))),wp.element.createElement("span",null,wp.element.createElement("button",{type:"button",className:"btn btn-"+w},"Star:",wp.element.createElement("form",{onSubmit:l},wp.element.createElement("select",{value:w,onChange:l},wp.element.createElement("option",{value:"none"},"None"),wp.element.createElement("option",{value:"gold"},"Gold"),wp.element.createElement("option",{value:"silver"},"Silver"),wp.element.createElement("option",{value:"bronze"},"Bronze"),wp.element.createElement("option",{value:"anti"},"Anti"))))))))},save:function(e){var t=e.attributes.className,n=(e.setAttributes,e.attributes),c=n.title,s=n.summary,i=n.queer,u=n.worthit,p=n.star,w=n.trigger;return wp.element.createElement(m,null,wp.element.createElement("div",{className:t+" bd-callout screener-shortcode"},wp.element.createElement("h5",null,"Screener Review On\xa0",wp.element.createElement(v.Content,{tagName:"em",value:c})),wp.element.createElement(v.Content,{tagName:"p",value:s}),wp.element.createElement("p",null,wp.element.createElement(l,{score:i}),"\xa0",wp.element.createElement(a,{score:u}),"\xa0",wp.element.createElement(r,{score:w}),"\xa0",wp.element.createElement(o,{score:p}))))}})},function(e,t){},function(e,t){}]);