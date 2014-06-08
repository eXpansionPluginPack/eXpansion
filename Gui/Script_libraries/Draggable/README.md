# Purpose
The Manialink library is a compilation of ManiaScript functions for your Manialink scripts. It offers different modules making it easier to create dynamic content : animations, tooltips, draggable elements, ...

# Demo
You can check the "library-manialink" Manialink ingame to see a demonstration of what you can do with this library.

# Usage
Each module can be used separately or combined with each other. Just be sure to include the MathLib and TextLib only once at the beginning of your script.

* [Animations](https://github.com/maniaplanet/library-manialink#animations)
* [Tooltips](https://github.com/maniaplanet/library-manialink#tooltips)
* [Draggable](https://github.com/maniaplanet/library-manialink#draggable)

## Animations

The first step  once you have pasted the animation module in your manialink script is to call the `LibManialink_AnimLoop()` regularly. Each time this function is called the animations will do one step. So if you want a fluid animation call it after each `yield` instruction.

Example of the basic setting :
```js
<frame id="Frame_Global">
  <quad sizen="15 15" halign="center" valign="center" bgcolor="047" id="Quad_Anim" />
  <label posn="-30 20" halign="center" style="CardButtonMedium" text="Anim 1" scriptevents="1" id="Button_Anim1" />
  <label posn="30 20" halign="center" style="CardButtonMedium" text="Anim 2" scriptevents="1" id="Button_Anim2" />
</frame>
<script><!--

// Paste the animations module here
// ...

main() {
  while (True) {
    yield;
    
    LibManialink_AnimLoop();
  }
}
--></script>
```

You have access to three animation functions:
* `LibManialink_Anim()` : clear the animation queue of an element (frame, quad, label, ...) and start a new animation sequence
* `LibManialink_AnimChain()` : add an animation at the end of the animation queue
* `LibManialink_AnimInsert()`: insert an animation inside the animation queue

Let's start with a simple animation. I want the quad to move to another position when I click on the "Anim 1" button :
```js 
<frame id="Frame_Global">
  <quad sizen="15 15" halign="center" valign="center" bgcolor="047" id="Quad_Anim" />
  <label posn="-30 20" halign="center" style="CardButtonMedium" text="Anim 1" scriptevents="1" id="Button_Anim1" />
  <label posn="30 20" halign="center" style="CardButtonMedium" text="Anim 2" scriptevents="1" id="Button_Anim2" />
</frame>
<script><!--

// Animation module
// ...

main() {
  while (True) {
    yield;
    
    LibManialink_AnimLoop();
    
    foreach (Event in PendingEvents) {
      if (Event.Type == CMlEvent::Type::MouseClick) {
        if (Event.ControlId == "Button_Anim1") {
          LibManialink_Anim("""<quad posn="50 -50" id="Quad_Anim" />""", 3000, "EaseOutBounce");
        }
      }
    }
  }
}
--></script>
```

The `LibManialink_Anim()` function take 3 parameters. The first one is the element and the properties we want to animate. In this case it's the position of the quad with an id equal to "Quad_Anim". The second parameter is the duration of the animation and the third the easing method.

Here is a list of the available easing methods :
* Linear
* Quad
* Cubic
* Quart
* Quint
* Sine
* Exp
* Circ
* Back
* Elastic
* Bounce

You can check [this website](http://easings.net/) to see how the different easings affect the animation.

All the animations start from the current properties of the animated element. This is the reason why when we click on the "Anim 1" button a second time the animation doesn't play anymore. The element is already at its desired position.

You can send the quad to it's original position with a second animation bind on the "Anim 2" button :
```js 
foreach (Event in PendingEvents) {
  if (Event.Type == CMlEvent::Type::MouseClick) {
    if (Event.ControlId == "Button_Anim1") {
      LibManialink_Anim("""<quad posn="50 -50" id="Quad_Anim" />""", 3000, "EaseOutBounce");
    } else if (Event.ControlId == "Button_Anim2") {
      LibManialink_Anim("""<quad posn="0 0" id="Quad_Anim" />""", 3000, "EaseInOutElastic");
    }
  }
}
```

If you press the opposing button while an animation is running you'll see that it will stop and the new animation start from the current position of the quad.

You can animate multiple properties during one animation :
```js 
foreach (Event in PendingEvents) {
  if (Event.Type == CMlEvent::Type::MouseClick) {
    if (Event.ControlId == "Button_Anim1") {
      LibManialink_Anim("""<quad posn="50 -50" sizen="10 30" scale="2" rot="45" bgcolor="f70" opacity="0.5" id="Quad_Anim" />""", 3000, "EaseOutBounce");
    } else if (Event.ControlId == "Button_Anim2") {
      LibManialink_Anim("""<quad posn="0 0" id="Quad_Anim" />""", 3000, "EaseInOutElastic");
    }
  }
}
```

If you press the "Anim 2" button after the "Anim 1" button you'll see that the quad position will return to it's original value but not the other properties. Not specifying a property in the animation function will leave it at it's current value. Which is the case in the second animation where we just specify the position property.

Now let's take a look at the `LibManialink_AnimChain()` function. It works exactly like `LibManialink_Anim()` but instead of clearing the animation queue of the element it will add a new animation at the end of the queue. So taking our previous example :
```js 
foreach (Event in PendingEvents) {
  if (Event.Type == CMlEvent::Type::MouseClick) {
    if (Event.ControlId == "Button_Anim1") {
      LibManialink_Anim("""<quad posn="50 -50" sizen="10 30" scale="2" rot="45" bgcolor="f70" opacity="0.5" id="Quad_Anim" />""", 3000, "EaseOutBounce");
      LibManialink_AnimChain("""<quad posn="-50 -50" rot="-45" bgcolor="7f7" opacity="1" id="Quad_Anim" />""", 2000, "");
    } else if (Event.ControlId == "Button_Anim2") {
      LibManialink_Anim("""<quad posn="0 0" id="Quad_Anim" />""", 3000, "EaseInOutElastic");
    }
  }
}
```

With this, when you click on the "Anim 1" button the quad will start to go to it's first position during 3 seconds and then move to the second one during 2 seconds.

But let's say you want to have an animation where the quad go from position A to B in 5 seconds and rotate during the translation for 3 seconds starting after one second. You'll have to use the `LibManialink_AnimInsert()` for that :
```js 
foreach (Event in PendingEvents) {
  if (Event.Type == CMlEvent::Type::MouseClick) {
    if (Event.ControlId == "Button_Anim1") {
      LibManialink_Anim("""<quad posn="100 0" id="Quad_Anim" />""", 5000, "");
      LibManialink_AnimInsert("""<quad rot="180" id="Quad_Anim" />""", 1000, 3000, "");
    } else if (Event.ControlId == "Button_Anim2") {
      LibManialink_Anim("""<quad posn="0 0" rot="0" id="Quad_Anim" />""", 3000, "EaseInOutElastic");
    }
  }
}
```

The function takes one more parameter than `LibManialink_Anim()`. The first parameter is still the element and the properties we want to animate. Then we have time at which the animation will start. Finally we have the duration of the animation and the easing method.

And now let's see what we could do by combining them all together :
```js 
foreach (Event in PendingEvents) {
  if (Event.Type == CMlEvent::Type::MouseClick) {
    if (Event.ControlId == "Button_Anim1") {
      LibManialink_Anim("""<quad posn="0 -40" id="Quad_Anim" />""", 3000, "EaseOutBounce");
      LibManialink_AnimInsert("""<quad rot="-2" id="Quad_Anim" />""", 0, 1000, "");
      LibManialink_AnimInsert("""<quad rot="90" id="Quad_Anim" />""", 1000, 1500, "");
      LibManialink_AnimChain("""<quad rot="45" id="Quad_Anim" />""", 2500, "EaseInOutExp");
      LibManialink_AnimChain("""<quad posn="0 -10" id="Quad_Anim" />""", 2500, "EaseInOutExp");
      LibManialink_AnimChain("""<quad scale="2" id="Quad_Anim" />""", 2500, "EaseOutBack");
      LibManialink_AnimChain("""<quad posn="-20 -20" sizen="30 10" bgcolor="f7f" id="Quad_Anim" />""", 2500, "EaseOutElastic");
    } else if (Event.ControlId == "Button_Anim2") {
      LibManialink_Anim("""<quad posn="0 0" sizen="15 15" scale="1" rot="0" bgcolor="047" id="Quad_Anim" />""", 1000, "EaseInOutElastic");
    }
  }
}
```

You can also repeat a whole animation for a definite number of time or indefinitely. You must wrap the animation you want to repeat between two functions, `LibManialink_AnimRepeatStart()` and `LibManialink_AnimRepeatEnd()` :
```js
foreach (Event in PendingEvents) {
  if (Event.Type == CMlEvent::Type::MouseClick) {
    if (Event.ControlId == "Button_Anim1") {
      LibManialink_AnimRepeatStart(1000, 3);
      LibManialink_Anim("""<quad scale="2" id="Quad_Anim" />""", 500, "EaseOutBack");
      LibManialink_AnimChain("""<quad scale="1" id="Quad_Anim" />""", 500, "EaseOutBack");
      LibManialink_AnimRepeatEnd();
    } else if (Event.ControlId == "Button_Anim2") {
      LibManialink_Anim("""<quad scale="1" rot="0" id="Quad_Anim" />""", 250, "EaseOutBack");
    }
  }
}
```

`LibManialink_AnimRepeatStart()` take two arguments :
* The time interval between each loop
* The number of repeat

In this case we want to repeat the animation each second three times.
`LibManialink_AnimRepeatStart()` can also take only the first argument and in this case the animation will be repeated indefinitely.

If you can't identify the element you want to animate by an unique id, the animations functions can take an optional parameter as first argument. You can pass the CMlControl directly to the function :
```js
declare MyControl <=> ((Page.MainFrame.Controls[0] as CMlFrame).Controls[0] as CMlFrame).Controls[0];
LibManialink_Anim(MyControl, """<quad posn="0 0" />""", 3000, "EaseInOutElastic");
```

To stop an animation on an element you can use the `LibManialink_AnimStop()` function. Two versions of the function exist. the first one take a CMlControl as argument while the second one take a Text, the ControlId of the control.
```js 
Void LibManialink_AnimStop(CMlControl _Control)

@param  _Control   The control to stop
```
```js 
Void LibManialink_AnimStop(Text _ControlId)

@param  _ControlId   The ControlId of the control to stop
```

If you want to know if an animation is currently running on a control you can use the `LibManialink_IsAnimated()` function. There's two versions of the function, the first one take a CMlControl as argument while the second one take a Text, the ControlId of the control. Both return a Boolean : True if an animation is running, False otherwise.
```js 
Boolean LibManialink_IsAnimated(CMlControl _Control)

@param  _Control   The control to test
```
```js 
Boolean LibManialink_IsAnimated(Text _ControlId)

@param  _ControlId   The ControlId of the control to test
```

Not all properties can be animated, this is the list of the available ones.
CMlControl :
- Position
- Size
- Scale
- Rotation

CMlQuad :
- Opacity
- Colorize
- BgColor

CMlLabel :
- Opacity
- TextColor

CMlGauge :
- Ratio
- Color

## Tooltips

The tooltips module allow to display a contextual help. In the next example we will display a small help text when the mouse is over a quad.

```js
<quad posn="0 0" sizen="10 10" halign="center" valign="center" bgcolor="fff" scriptevents="1" class="LibManialink_TooltipShow" id="Tooltip_Default" />
<frame hidden="1" class="LibManialink_Tooltip" id="Tooltip_Default">
  <label posn="0 0 1" sizen="48 4" halign="center" valign="center2" textsize="1.5" textcolor="aaa" id="Tooltip_Message" />
  <quad sizen="50 6" halign="center" valign="center" bgcolor="000d" id="Tooltip_BoundingBox" />
</frame>

<script><!--

// Paste the tooltip module here
// ...

main() {
  LibManialink_SetTooltipMessage("Tooltip_Default", "This is the default tooltip.");

  while (True) {
    yield;
    
    LibManialink_TooltipLoop();
  }
}
--></script>
```

First you must include the tooltip module in your script. Then add the class `LibManialink_TooltipShow` on the quad that will display the tooltip when the mouse is over it. Create the tooltip frame with the class `LibManialink_Tooltip` associated to it. The `LibManialink_TooltipShow` quad and `LibManialink_Tooltip` frame must share the same id. The tooltip frame can contains two elements:
* A label to display the tooltip text with the id "Tooltip_Message".
* A quad used as a bouding box with the id "Tooltip_BoundingBox" that will prevent the tooltip to go outside of the screen. 

Other than that you can display what you want in your tooltip. You can have any number of tooltip frames in the manialink, so you can create different styles to fit your needs. The tooltip frame can be positioned anywhere in the manialink, it will be automatically aligned with the corresponding quad when shown.

Once the tootltip frame is in your manialink you just have to add the `LibManialink_TooltipLoop()` function in your script where it will be called at each frame.
The `Tooltip_Message` label in the tooltip frame can be changed from the script with the `LibManialink_SetTooltipMessage()` function. It takes two parameters :
* The id of the tooltip to update
* The text to display in the tooltip


## Draggable

With this module you can create frames that will be draggable by the player. The frames can be constrained to a specific area or moved freely. In the example below we will create a simple constrained frame.

```js
<frame posn="-120 20 15" class="LibManialink_Draggable" id="Drag_1">
  <quad posn="0 0 1" sizen="30 10" valign="center" bgcolor="700" scriptevents="1" class="LibManialink_DraggableHandle" id="Drag_1" />
  <quad posn="0 0" sizen="30 30" valign="center" bgcolor="000" class="LibManialink_DraggableBoundingBox" id="Drag_1" />
</frame>
<quad posn="-139 59 1" sizen="68 98" bgcolor="772" class="LibManialink_DraggableArea" id="Drag_1" />

<script><!--

// Paste the draggable module here
// ...

main() {
  while (True) {
    yield;
    
    LibManialink_DraggableLoop();
  }
}
--></script>
```

We start by creating the frame that will be draggable. This frame has the `LibManialink_Draggable` class and an id to tell it apart from other frames. This frame contains two quads. 

One with the class `LibManialink_DraggableHandle` and the same id than the frame. Whenever the player click on this quad, he will start dragging any control with the same id and the `LibManialink_Draggable` class (can be a frame, a quad, a label, ...) even outside of the frame.

The second quad uses the `LibManialink_DraggableBoundingBox` class and the same id than the frame. It's used as a bounding box, preventing the frame from going offscreen or outside of the designated area.

Eventually there's a quad outside of the draggable frame with the `LibManialink_DraggableArea` class and once again the same id than the frame. This quad will define the area where the bouding box of the frame can go.

Once your controls are set up you can add the `LibManialink_DraggableLoop()` function in your script where it will be called each frame.