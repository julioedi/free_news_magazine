
const $ = (string) => {
  return document.querySelector(string)
};
Object.assign($,{all:(string) =>{
  const $el = document.querySelectorAll(string);
  return !$el ? null : [...$el];
}});
( () =>{
  const $search_icon = $("#main_search .clickable");
} )();
// (() =>{
//
//   const $search_icon = document.querySelector()
// })();
