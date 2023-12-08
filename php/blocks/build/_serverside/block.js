(()=>{"use strict";var e,t={28:(e,t,a)=>{const l=window.wp.blocks,r=window.React,n=window.wp.element,o=window.wp.components,s=window.wp.blockEditor,c=window.wp.serverSideRender;var i=a.n(c);class v extends n.Component{render(){const{attributes:e,setAttributes:t}=this.props,{users:a,format:l}=e,c=(0,r.createElement)(s.InspectorControls,null,(0,r.createElement)(o.PanelBody,{title:"Team Member Settings"},(0,r.createElement)(o.TextControl,{label:"Username",help:"Username or ID of team member (i.e. liljimmi, ipstenu, saralance)",value:a,onChange:e=>t({users:e})}),(0,r.createElement)(o.SelectControl,{label:"Card Format",type:"string",value:l,options:[{label:"Large",value:"large"},{label:"Compact",value:"compact"},{label:"Thumbnail",value:"thumbnail"}],onChange:e=>t({format:e})})));return(0,r.createElement)(n.Fragment,null,c,(0,r.createElement)(i(),{block:"lwtv/author-box",attributes:e}))}}const m=v;(0,l.registerBlockType)("lwtv/author-box",{title:"Team Member",icon:function(){return(0,r.createElement)("svg",{"aria-hidden":"true","data-prefix":"fas","data-icon":"portrait",role:"img",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 384 512",className:"svg-inline--fa fa-portrait fa-w-12 fa-3x"},(0,r.createElement)("path",{fill:"currentColor",d:"M336 0H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V48c0-26.5-21.5-48-48-48zM192 128c35.3 0 64 28.7 64 64s-28.7 64-64 64-64-28.7-64-64 28.7-64 64-64zm112 236.8c0 10.6-10 19.2-22.4 19.2H102.4C90 384 80 375.4 80 364.8v-19.2c0-31.8 30.1-57.6 67.2-57.6h5c12.3 5.1 25.7 8 39.8 8s27.6-2.9 39.8-8h5c37.1 0 67.2 25.8 67.2 57.6v19.2z"}))},category:"lezwatch",className:!1,attributes:{users:{type:"string"},format:{type:"string",default:"large"}},edit:m,save:()=>null}),(0,l.registerBlockType)("lez-library/glossary",{title:"Glossary",icon:function(){return(0,r.createElement)("svg",{"aria-hidden":"true","data-prefix":"fas","data-icon":"boxes",role:"img",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 576 512",className:"svg-inline--fa fa-boxes fa-w-18 fa-2x"},(0,r.createElement)("path",{fill:"currentColor",d:"M560 288h-80v96l-32-21.3-32 21.3v-96h-80c-8.8 0-16 7.2-16 16v192c0 8.8 7.2 16 16 16h224c8.8 0 16-7.2 16-16V304c0-8.8-7.2-16-16-16zm-384-64h224c8.8 0 16-7.2 16-16V16c0-8.8-7.2-16-16-16h-80v96l-32-21.3L256 96V0h-80c-8.8 0-16 7.2-16 16v192c0 8.8 7.2 16 16 16zm64 64h-80v96l-32-21.3L96 384v-96H16c-8.8 0-16 7.2-16 16v192c0 8.8 7.2 16 16 16h224c8.8 0 16-7.2 16-16V304c0-8.8-7.2-16-16-16z"}))},category:"lezwatch",className:!1,attributes:{taxonomy:{type:"string"}},edit:e=>{const{attributes:t,setAttributes:a}=(void 0).props,{taxonomy:l}=t;return(0,r.createElement)(n.Fragment,null,(0,r.createElement)(s.InspectorControls,null,(0,r.createElement)(o.PanelBody,{title:"Glossary Block Settings"},(0,r.createElement)(o.SelectControl,{label:"Taxonomy",value:l,options:[{label:"Choose a taxonomy...",value:null},{label:"Clichés",value:"lez_cliches"},{label:"Tropes",value:"lez_tropes"},{label:"Formats",value:"lez_formats"},{label:"Genres",value:"lez_genres"},{label:"Intersections",value:"lez_intersections"}],onChange:e=>a({taxonomy:e})}))),(0,r.createElement)(c.ServerSideRender,{block:"lez-library/glossary",attributes:e.attributes}))},save:()=>null}),(0,l.registerBlockType)("lwtv/tvshow-calendar",{title:"TV Shows Calendar",icon:function(){return(0,r.createElement)("svg",{"aria-hidden":"true",focusable:"false","data-prefix":"fas","data-icon":"calendar-week",role:"img",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 448 512",className:"svg-inline--fa fa-calendar-week fa-w-14 fa-3x"},(0,r.createElement)("path",{fill:"currentColor",d:"M0 464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48V192H0v272zm64-192c0-8.8 7.2-16 16-16h288c8.8 0 16 7.2 16 16v64c0 8.8-7.2 16-16 16H80c-8.8 0-16-7.2-16-16v-64zM400 64h-48V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H160V16c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v48H48C21.5 64 0 85.5 0 112v48h448v-48c0-26.5-21.5-48-48-48z"}))},category:"lezwatch",keywords:["calendar","tv shows"],className:!1,edit:()=>(0,r.createElement)(i(),{block:"lwtv/tvshow-calendar"}),save:()=>null}),(0,l.registerBlockType)("lez-library/private-note",{title:"Private Note",icon:function(){return(0,r.createElement)("svg",{"aria-hidden":"true","data-prefix":"fas","data-icon":"user-secret",role:"img",xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 448 512",className:"svg-inline--fa fa-user-secret fa-w-14 fa-3x"},(0,r.createElement)("path",{fill:"currentColor",d:"M383.9 308.3l23.9-62.6c4-10.5-3.7-21.7-15-21.7h-58.5c11-18.9 17.8-40.6 17.8-64v-.3c39.2-7.8 64-19.1 64-31.7 0-13.3-27.3-25.1-70.1-33-9.2-32.8-27-65.8-40.6-82.8-9.5-11.9-25.9-15.6-39.5-8.8l-27.6 13.8c-9 4.5-19.6 4.5-28.6 0L182.1 3.4c-13.6-6.8-30-3.1-39.5 8.8-13.5 17-31.4 50-40.6 82.8-42.7 7.9-70 19.7-70 33 0 12.6 24.8 23.9 64 31.7v.3c0 23.4 6.8 45.1 17.8 64H56.3c-11.5 0-19.2 11.7-14.7 22.3l25.8 60.2C27.3 329.8 0 372.7 0 422.4v44.8C0 491.9 20.1 512 44.8 512h358.4c24.7 0 44.8-20.1 44.8-44.8v-44.8c0-48.4-25.8-90.4-64.1-114.1zM176 480l-41.6-192 49.6 32 24 40-32 120zm96 0l-32-120 24-40 49.6-32L272 480zm41.7-298.5c-3.9 11.9-7 24.6-16.5 33.4-10.1 9.3-48 22.4-64-25-2.8-8.4-15.4-8.4-18.3 0-17 50.2-56 32.4-64 25-9.5-8.8-12.7-21.5-16.5-33.4-.8-2.5-6.3-5.7-6.3-5.8v-10.8c28.3 3.6 61 5.8 96 5.8s67.7-2.1 96-5.8v10.8c-.1.1-5.6 3.2-6.4 5.8z"}))},category:"lezwatch",description:"Private notes, only seen by logged in users. It will be stripped from all published pages.",edit:e=>{const{className:t}=e;return(0,r.createElement)(n.Fragment,null,(0,r.createElement)("div",{className:`${t} alert alert-warning`},(0,r.createElement)(s.InnerBlocks,{template:[["core/paragraph",{content:"All content in this block will be invisible to non-logged-in visitors (delete this and replace it)."}]],templateLock:!1})))},save:e=>{const{attributes:{className:t}}=e;return(0,r.createElement)("div",{className:`${t} alert alert-warning`},(0,r.createElement)(s.InnerBlocks.Content,null))}})}},a={};function l(e){var r=a[e];if(void 0!==r)return r.exports;var n=a[e]={exports:{}};return t[e](n,n.exports,l),n.exports}l.m=t,e=[],l.O=(t,a,r,n)=>{if(!a){var o=1/0;for(v=0;v<e.length;v++){for(var[a,r,n]=e[v],s=!0,c=0;c<a.length;c++)(!1&n||o>=n)&&Object.keys(l.O).every((e=>l.O[e](a[c])))?a.splice(c--,1):(s=!1,n<o&&(o=n));if(s){e.splice(v--,1);var i=r();void 0!==i&&(t=i)}}return t}n=n||0;for(var v=e.length;v>0&&e[v-1][2]>n;v--)e[v]=e[v-1];e[v]=[a,r,n]},l.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return l.d(t,{a:t}),t},l.d=(e,t)=>{for(var a in t)l.o(t,a)&&!l.o(e,a)&&Object.defineProperty(e,a,{enumerable:!0,get:t[a]})},l.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={150:0,526:0};l.O.j=t=>0===e[t];var t=(t,a)=>{var r,n,[o,s,c]=a,i=0;if(o.some((t=>0!==e[t]))){for(r in s)l.o(s,r)&&(l.m[r]=s[r]);if(c)var v=c(l)}for(t&&t(a);i<o.length;i++)n=o[i],l.o(e,n)&&e[n]&&e[n][0](),e[n]=0;return l.O(v)},a=globalThis.webpackChunklwtv_blocks=globalThis.webpackChunklwtv_blocks||[];a.forEach(t.bind(null,0)),a.push=t.bind(null,a.push.bind(a))})();var r=l.O(void 0,[526],(()=>l(28)));r=l.O(r)})();