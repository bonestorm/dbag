

require(['db_interface','grid','input','help','menu','text_overlay','composer','slist'],function(DB_INTERFACE,GRID,INPUT,HELP,MENU,TEXT_OVERLAY,COMPOSER,SLIST){


    var canvas_obj = $('#canvasOne');
    var overlay_obj = $('#text_overlay');
    var theCanvas = canvas_obj.get(0);//("canvasOne");
    var cursor;

    var OBJ = {
        shiftKey: false,
        canvas_obj = canvas_obj,
        overlay_obj = overlay_obj
    };
    
  
    //application globals
    //passed to most objects
    var globs = {
  //  background_color: "#f0F5FF",
      grid_background_color: "#ffFfFF",
      background_color: "#f0f5ff",
      rgba_background_color: "rgba(240,245,255",
      cell_size: 16,
      quads_wide: 16*4,quads_high: 12*4,//number of cells wide and high the whole grid is
      border: 50,//border width around the grid
      margin: {x:0,y:300},//top and left empty space
      context: theCanvas.getContext('2d')//2d canvas context
    };

  
    OBJ.change_cursor = function(new_style){
        if(cursor != new_style){
            $("#canvasOne")[0].style.cursor = new_style;
            cursor = new_style;
        }
    }
    OBJ.change_cursor("auto");


    OBJ.resize = function(width,height){
  
      var new_dims = {width:0, height: 0};//this is passed back so the resize of the actual canvas element can be made
  
      var fluff_width = globs.border*2+globs.margin.x;
      var fluff_height = globs.border*2+globs.margin.y;
  
      var quad_size = globs.cell_size*4;
  
      var grid_width = width-fluff_width;
      var grid_height = height-fluff_height;
      var qwidth = Math.floor(grid_width/quad_size);
      var qheight = Math.floor(grid_height/quad_size);
      new_dims.width = qwidth*quad_size+fluff_width;
      new_dims.height = qheight*quad_size+fluff_height;
      globs.width = globs.cell_size*4*qwidth;
      globs.height = globs.cell_size*4*qheight;
      globs.win_quads_wide = qwidth;
      globs.win_quads_high = qheight;
      //the limits for the window position (min limit is 0)
      globs.over_wide = globs.quads_wide-globs.win_quads_wide;
      globs.over_high = globs.quads_high-globs.win_quads_high;
  
      if(globs.grid !== undefined){globs.grid.resize(qwidth,qheight);}
      if(globs.slist !== undefined){globs.slist.resize(new_dims.width);}
      if(globs.help !== undefined){globs.help.resize(new_dims.width);}
      if(globs.composer !== undefined){globs.composer.resize(new_dims.width);}
  
      return new_dims;
  
    }
  
    OBJ.resize_to_browser = function(){
      return OBJ.resize(window.innerWidth,window.innerHeight);
    }
  
    OBJ.window_resizing = false;
  
    function window_resize(){
      var new_dims = OBJ.resize_to_browser();
      canvas_obj.attr("width",new_dims.width+"px");
      canvas_obj.attr("height",new_dims.height+"px");
      overlay_obj.css("width",new_dims.width+"px");
      overlay_obj.css("height",new_dims.height+"px");
      OBJ.refresh();
      OBJ.window_resizing = false;
    }
  
    //to resize the canvas element when the browser window is resized
    $(window).resize(function() {
      if(!OBJ.window_resizing){
        OBJ.window_resizing = true;
        setTimeout(window_resize,1000);
      }
    }
  
    //sets all the dimension variables, sizes the canvas element, and then redraws the screen
    window_resize();
  
  
    //document should have the same background color as the canvas (maybe just make canvas transparent?)
    $("body").css("background-color",globs.background_color);
  
    //simple interface to the database
    var _db_interface = new DB_INTERFACE(globs);
    globs.db_interface = _db_interface;
  
    //to access the divs that are put on top of the canvas for purpose of displaying text.  many classes should access
    var _text_overlay = new TEXT_OVERLAY(globs);
    globs.text_overlay = _text_overlay;
   
    var _input = new INPUT(globs);
    globs.input = _input;//most object in the application need access to the mouse
    
    var _menu = new MENU(globs);
    globs.menu = _menu;
    
    var _help = new HELP(globs);
    globs.help = _help;
  
  
    var _slist = new SLIST(globs);
    globs.slist = _slist;
  
    //composer is initialized after slist so composer can adjust its size based on the slist's size
    var _composer = new COMPOSER(globs);
    globs.composer = _composer;
  
  
    var _grid = new GRID(globs);
    globs.grid = _grid;
  
  
    _menu.add_handler('new_join',_grid.new_join);//add the handler for making a new link
  
    
    function drawScreen() {
      
      globs.context.save();
      globs.context.setTransform(1,0,0,1,globs.border+globs.margin.x,globs.border+globs.margin.y);//simple translation
      
      globs.context.globalAlpha = 1.0;
      context.clearRect(
        -globs.border-globs.margin.x,-globs.border-globs.margin.y,
        globs.width+globs.border*2,globs.margin.y+globs.height+globs.border*2
      );
      
      _grid.draw();
  
      _composer.draw();
  
      _slist.draw();
      
      if(_help.active){
        _help.draw();
      }
  
      globs.context.restore();
    }
  
    //other objects should be able to redraw the screen
    //could possibly add something so a call to refresh will start a timer and all refreshes for a bit will be ignored
    globs.refresh = drawScreen;
    OBJ.refresh = globs.refresh;
  
    return OBJ;

});
