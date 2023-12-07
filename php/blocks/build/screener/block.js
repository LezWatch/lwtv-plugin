(()=>{"use strict";var e,t={259:()=>{const e=JSON.parse('{"u2":"lwtv/screener"}'),t=window.React,n=window.wp.element,a=window.wp.blockEditor;function r({score:e}){return(0,t.createElement)(n.Fragment,null,(0,t.createElement)("span",{"data-bs-toggle":"tooltip","aria-label":"How good is this show for queers?",title:"","data-original-title":"How good is this show for queers?"},(0,t.createElement)("button",{type:"button",className:"btn btn-dark"},"Queer Score: ",`${e}`)))}function l({score:e}){let a,r="info",l="meh";switch(e){case"yes":r="success",l="thumbs-up",a="M3,9a1,1,0,0,0-1,1V21a1,1,0,0,0,2,0V10A1,1,0,0,0,3,9ZM20,9H12.37l1.48-3.89A2.35,2.35,0,0,0,13,2.38,2.06,2.06,0,0,0,10.11,3L6,9H6v9a4,4,0,0,0,4,4h6.7a2,2,0,0,0,1.83-1.19l3.3-7.42a2.06,2.06,0,0,0,.17-.81V11A2,2,0,0,0,20,9Z";break;case"no":r="danger",l="thumbs-down",a="M14,2H7.3A2,2,0,0,0,5.47,3.19l-3.3,7.42a2.06,2.06,0,0,0-.17.81V13a2,2,0,0,0,2,2h7.63l-1.48,3.89A2.35,2.35,0,0,0,11,21.62a2.06,2.06,0,0,0,2.93-.57L18,15h0V6A4,4,0,0,0,14,2Zm7,0a1,1,0,0,0-1,1V14a1,1,0,0,0,2,0V3A1,1,0,0,0,21,2Z";break;case"tbd":r="info",l="clock-icon",a="M12,5a1,1,0,0,0-1,1V8H9a1,1,0,0,0,0,2h4V6A1,1,0,0,0,12,5ZM23,22H1a1,1,0,1,0,0,2H23a1,1,0,0,0,0-2ZM21,9a9,9,0,1,0-18,0L3,20H21Zm-9,7a7,7,0,1,1,7-7A7,7,0,0,1,12,16Z";break;default:a="M12,0A12,12,0,1,0,24,12,12,12,0,0,0,12,0ZM7.5,8A1.5,1.5,0,1,1,6,9.5,1.5,1.5,0,0,1,7.5,8ZM17,17H7a1,1,0,0,1,0-2H17a1,1,0,0,1,0,2Zm-.5-6A1.5,1.5,0,1,1,18,9.5,1.5,1.5,0,0,1,16.5,11Z"}const o=(0,t.createElement)("title",null,l);return(0,t.createElement)(n.Fragment,null,(0,t.createElement)("span",{"data-bs-toggle":"tooltip","aria-label":`Is this show worth watching? ${e}`,title:"","data-original-title":`Is this show worth watching? ${e}`},(0,t.createElement)("button",{type:"button",className:`btn btn-${r}`},"Worth It? ",(0,t.createElement)("span",{role:"img",className:`screener screener-worthit ${e}`},(0,t.createElement)("span",{className:"symbolicon",role:"img"},(0,t.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24",id:l},o,(0,t.createElement)("path",{d:a})))))))}function o(){return(0,t.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},(0,t.createElement)("title",null,"warning"),(0,t.createElement)("g",{id:"warning"},(0,t.createElement)("path",{d:"M23.51,17.5,15.18,2.85a3.66,3.66,0,0,0-6.36,0L.49,17.5A3.68,3.68,0,0,0,3.67,23H20.33A3.68,3.68,0,0,0,23.51,17.5ZM11,7a1,1,0,0,1,1-1h0a1,1,0,0,1,1,1v7a1,1,0,0,1-1,1h0a1,1,0,0,1-1-1Zm1,13a1.5,1.5,0,1,1,1.5-1.5A1.5,1.5,0,0,1,12,20Z"})))}function c({score:e}){let a;switch(e){case"high":a="danger";break;case"medium":a="warning";break;default:a="info"}if("none"!==e)return(0,t.createElement)(n.Fragment,null,(0,t.createElement)("span",{"data-bs-toggle":"tooltip","aria-label":"Warning - This show contains triggers",title:"Warning - This show contains triggers"},(0,t.createElement)("button",{type:"button",className:`btn btn-${a}`},(0,t.createElement)("span",{role:"img",className:`screener screener-warn ${a}`},(0,t.createElement)("span",{className:"symbolicon",role:"img"},o)))))}function i(){return(0,t.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},(0,t.createElement)("title",null,"star"),(0,t.createElement)("g",{id:"star"},(0,t.createElement)("path",{d:"M24,9.69A1,1,0,0,0,23,9H15.39L13,1.68a1,1,0,0,0-1.9,0L8.61,9H1a1,1,0,0,0-.95.69,1,1,0,0,0,.36,1.12l6.12,4.45L4.05,22.68a1,1,0,0,0,.36,1.12,1,1,0,0,0,1.17,0L12,19.23l6.42,4.58A1,1,0,0,0,19,24a1,1,0,0,0,.59-.2A1,1,0,0,0,20,22.68l-2.48-7.42,6.12-4.45A1,1,0,0,0,24,9.69Z"})))}function s({score:e}){let a;switch(e){case"anti":case"bronze":a="danger";break;case"silver":a="warning";break;default:a="gold"}if("none"!==e)return(0,t.createElement)(n.Fragment,null,(0,t.createElement)("span",{"data-bs-toggle":"tooltip","aria-label":`${e} Star Show`,title:"","data-original-title":`${e} Star Show`},(0,t.createElement)("button",{type:"button",className:"btn btn-info"},(0,t.createElement)("span",{role:"img",className:`screener screener-star ${a}`},(0,t.createElement)("span",{className:"symbolicon",role:"img"},i)))))}(0,window.wp.blocks.registerBlockType)(e.u2,{icon:function(){return(0,t.createElement)("svg",{xmlns:"http://www.w3.org/2000/svg",height:"1em",viewBox:"0 0 576 512",role:"img","data-icon":"video"},(0,t.createElement)("path",{fill:"currentColor",d:"M0 128C0 92.7 28.7 64 64 64H320c35.3 0 64 28.7 64 64V384c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V128zM559.1 99.8c10.4 5.6 16.9 16.4 16.9 28.2V384c0 11.8-6.5 22.6-16.9 28.2s-23 5-32.9-1.6l-96-64L416 337.1V320 192 174.9l14.2-9.5 96-64c9.8-6.5 22.4-7.2 32.9-1.6z"}))},edit:function(e){const{attributes:{title:r,summary:l,queer:o,worthit:c,star:i,trigger:s},setAttributes:m}=e;function u(e){const t=e.target.querySelector("option:checked");m({queer:t.value}),e.preventDefault()}function g(e){const t=e.target.querySelector("option:checked");m({worthit:t.value}),e.preventDefault()}function p(e){const t=e.target.querySelector("option:checked");m({star:t.value}),e.preventDefault()}function h(e){const t=e.target.querySelector("option:checked");m({trigger:t.value}),e.preventDefault()}return(0,t.createElement)(n.Fragment,null,(0,t.createElement)("div",{className:"wp-block lwtv-screener bd-callout screener-shortcode"},(0,t.createElement)("h5",null,"Screener Review On ... ",(0,t.createElement)(a.PlainText,{tagName:"em",value:r,placeholder:"Show Title",onChange:function(e){m({title:e})}})),(0,t.createElement)(a.RichText,{tagName:"p",value:l,placeholder:"Content of Review",onChange:function(e){m({summary:e})}}),(0,t.createElement)("p",null,(0,t.createElement)("span",null,(0,t.createElement)("button",{type:"button",className:"btn btn-dark"},"Queer:",(0,t.createElement)("form",{onSubmit:u},(0,t.createElement)("select",{value:o,onChange:u},(0,t.createElement)("option",{value:"0"},"0"),(0,t.createElement)("option",{value:"1"},"1"),(0,t.createElement)("option",{value:"2"},"2"),(0,t.createElement)("option",{value:"3"},"3"),(0,t.createElement)("option",{value:"4"},"4"),(0,t.createElement)("option",{value:"5"},"5"))))),(0,t.createElement)("span",null,(0,t.createElement)("button",{type:"button",className:`btn btn-${c}`},"Worth:",(0,t.createElement)("form",{onSubmit:g},(0,t.createElement)("select",{value:c,onChange:g},(0,t.createElement)("option",{value:"yes"},"Yes"),(0,t.createElement)("option",{value:"meh"},"Meh"),(0,t.createElement)("option",{value:"no"},"No"),(0,t.createElement)("option",{value:"tbd"},"TBD"))))),(0,t.createElement)("span",null,(0,t.createElement)("button",{type:"button",className:`btn btn-${s}`},"Trigger:",(0,t.createElement)("form",{onSubmit:h},(0,t.createElement)("select",{value:s,onChange:h},(0,t.createElement)("option",{value:"none"},"None"),(0,t.createElement)("option",{value:"low"},"Low"),(0,t.createElement)("option",{value:"medium"},"Medium"),(0,t.createElement)("option",{value:"high"},"High"))))),(0,t.createElement)("span",null,(0,t.createElement)("button",{type:"button",className:`btn btn-${i}`},"Star:",(0,t.createElement)("form",{onSubmit:p},(0,t.createElement)("select",{value:i,onChange:p},(0,t.createElement)("option",{value:"none"},"None"),(0,t.createElement)("option",{value:"gold"},"Gold"),(0,t.createElement)("option",{value:"silver"},"Silver"),(0,t.createElement)("option",{value:"bronze"},"Bronze"),(0,t.createElement)("option",{value:"anti"},"Anti"))))))))},save:function(e){const{attributes:{className:o}}=e,{title:i,summary:m,queer:u,worthit:g,star:p,trigger:h}=e.attributes;return(0,t.createElement)(n.Fragment,null,(0,t.createElement)("div",{className:`${o} bd-callout screener-shortcode`},(0,t.createElement)("h5",null,"Screener Review On ",(0,t.createElement)(a.RichText.Content,{tagName:"em",value:i})),(0,t.createElement)(a.RichText.Content,{tagName:"p",value:m}),(0,t.createElement)("p",null,(0,t.createElement)(r,{score:u})," ",(0,t.createElement)(l,{score:g})," ",(0,t.createElement)(c,{score:h})," ",(0,t.createElement)(s,{score:p}))))}})}},n={};function a(e){var r=n[e];if(void 0!==r)return r.exports;var l=n[e]={exports:{}};return t[e](l,l.exports,a),l.exports}a.m=t,e=[],a.O=(t,n,r,l)=>{if(!n){var o=1/0;for(m=0;m<e.length;m++){for(var[n,r,l]=e[m],c=!0,i=0;i<n.length;i++)(!1&l||o>=l)&&Object.keys(a.O).every((e=>a.O[e](n[i])))?n.splice(i--,1):(c=!1,l<o&&(o=l));if(c){e.splice(m--,1);var s=r();void 0!==s&&(t=s)}}return t}l=l||0;for(var m=e.length;m>0&&e[m-1][2]>l;m--)e[m]=e[m-1];e[m]=[n,r,l]},a.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{var e={657:0,573:0};a.O.j=t=>0===e[t];var t=(t,n)=>{var r,l,[o,c,i]=n,s=0;if(o.some((t=>0!==e[t]))){for(r in c)a.o(c,r)&&(a.m[r]=c[r]);if(i)var m=i(a)}for(t&&t(n);s<o.length;s++)l=o[s],a.o(e,l)&&e[l]&&e[l][0](),e[l]=0;return a.O(m)},n=globalThis.webpackChunklwtv_blocks=globalThis.webpackChunklwtv_blocks||[];n.forEach(t.bind(null,0)),n.push=t.bind(null,n.push.bind(n))})();var r=a.O(void 0,[573],(()=>a(259)));r=a.O(r)})();