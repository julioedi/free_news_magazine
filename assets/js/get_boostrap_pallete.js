const list = document.querySelector(".font-monospace");
const data = {};
const keys = [100,200,300,400,500,600,700,800,900];

const componentToHex = (c) => {
  var hex = c.toString(16);
  return hex.length == 1 ? "0" + hex : hex;
}

const rgbToHex = (r, g, b) => {
  return "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
}



[...list.children].forEach(listColor => {
  [...listColor.children].forEach(color => {
    const basename = color.textContent.replace(/\#\w+/,"").replace(/\n/,"").trim().replace(/^\$/,"");
    const name = basename.split('-')[0];
    const styles = getComputedStyle(color);
    const preColor = styles.backgroundColor.replace(/^.*?\((.*?)\)/,"$1").replaceAll(/\s+/g,"").split(',').map(i => parseInt(i));
    const colorvalues = rgbToHex(preColor[0],preColor[1],preColor[2]);
    if (!(name in data)) {
      data[name] = {
        color: colorvalues,
      };
    }else{
      const code = basename.split('-')[1] ?? null;
      if(code){
        data[name][code] = colorvalues
      }
    }
  });
});
console.log(data);
