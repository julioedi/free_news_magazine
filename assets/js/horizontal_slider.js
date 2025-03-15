(() =>{
  const sliderList = [...document.querySelectorAll('.theme_horizontal_slider')];
  if (sliderList.length == 0) {
    return;
  }

  sliderList.forEach(slider => {
    let isDragging = false;
    let pressed = false;
    let left = 0;
    const sliderThumb = slider.parentNode;
    sliderThumb.addEventListener("mousedown", (e) => {
      isDragging = true;
      let moved = false;
      let initialLeft = sliderThumb.scrollLeft;
      // pressed = e.clientX - sliderThumb.getBoundingClientRect().left;
      const offsetX = e.clientX - sliderThumb.getBoundingClientRect().left;

      const onMouseMove = (moveEvent) => {
        moved = true;
        if (isDragging) {
          let newLeft = moveEvent.clientX - offsetX - sliderThumb.getBoundingClientRect().left;
          newLeft = newLeft * (-1);
          sliderThumb.scrollLeft = initialLeft  + newLeft;
        }
      };

      const onMouseUp = (e) => {
        isDragging = false;
        if (moved) {
          const got_link = e.target.closest("a");
          console.log(got_link);
        }
        document.body.style.userSelect = ""; // Re-enable text selection
        document.removeEventListener("mousemove", onMouseMove);
        document.removeEventListener("mouseup", onMouseUp);
      };

      document.addEventListener("mousemove", onMouseMove);
      document.addEventListener("mouseup", onMouseUp);
    })

    // sliderThumb.addEventListener("mousedown", (e) => {
    //   pressed = false;
    // })
    //
    // sliderThumb.addEventListener("mousemove", (e) => {
    //   if (!pressed) {
    //     return;
    //   }
    //   console.log(e);
    // })


  });


})()
