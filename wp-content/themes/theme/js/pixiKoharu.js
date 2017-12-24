if(typeof(moc_path) !== 'undefined'){ PIXI.loader.add('moc', theme_path + moc_path, { xhrType: PIXI.loaders.Resource.XHR_RESPONSE_TYPE.BUFFER }); }
if(typeof(tex1_path) !== 'undefined'){ PIXI.loader.add('texture1', theme_path + tex1_path); }
if(typeof(tex2_path) !== 'undefined'){ PIXI.loader.add('texture2', theme_path + tex2_path); }
if(typeof(tex3_path) !== 'undefined'){ PIXI.loader.add('texture3', theme_path + tex3_path); }
if(typeof(mot1_path) !== 'undefined'){ PIXI.loader.add('motion1', theme_path + mot1_path, { xhrType: PIXI.loaders.Resource.XHR_RESPONSE_TYPE.JSON }); }
if(typeof(mot2_path) !== 'undefined'){ PIXI.loader.add('motion2', theme_path + mot2_path, { xhrType: PIXI.loaders.Resource.XHR_RESPONSE_TYPE.JSON }); }
if(typeof(mot3_path) !== 'undefined'){ PIXI.loader.add('motion3', theme_path + mot3_path, { xhrType: PIXI.loaders.Resource.XHR_RESPONSE_TYPE.JSON }); }
if(typeof(phy_path) !== 'undefined'){ PIXI.loader.add('physics', theme_path + phy_path, { xhrType: PIXI.loaders.Resource.XHR_RESPONSE_TYPE.JSON }); }
PIXI.loader.load(function (loader, resources) {
	var app = new PIXI.Application(1280, 720, { transparent: true });

	//モデルの位置の基準になるHTML要素
	var canvas = document.querySelector(attach_tag);
	canvas.appendChild(app.view);
	var moc = LIVE2DCUBISMCORE.Moc.fromArrayBuffer(resources['moc'].data);
	var builder = new LIVE2DCUBISMPIXI.ModelBuilder();
	builder.setMoc(moc);
	builder.setTimeScale(1);

	if(resources['texture1']){ builder.addTexture(0, resources['texture1'].texture); }
	if(resources['texture2']){ builder.addTexture(1, resources['texture2'].texture); }
	if(resources['texture3']){ builder.addTexture(2, resources['texture3'].texture); }
	if(resources['physics']){ builder.setPhysics3Json(resources['physics'].data); }
	var model = builder.build();
	app.stage.addChild(model);
	app.stage.addChild(model.masks);

	var motions = [];
	if(resources['motion1']){ motions.push(LIVE2DCUBISMFRAMEWORK.Animation.fromMotion3Json(resources['motion1'].data)); }
	if(resources['motion2']){ motions.push(LIVE2DCUBISMFRAMEWORK.Animation.fromMotion3Json(resources['motion2'].data)); }
	if(resources['motion3']){ motions.push(LIVE2DCUBISMFRAMEWORK.Animation.fromMotion3Json(resources['motion3'].data)); }
	if(motions.length > 0){
		model.animator.addLayer("motion", LIVE2DCUBISMFRAMEWORK.BuiltinAnimationBlenders.OVERRIDE, 1.0);
		model.animator.getLayer("motion").play(motions[0]);
	}

	var rect = canvas.getBoundingClientRect();
	var center_x = pos_x + rect.left, center_y = pos_y + rect.top;
	var mouse_x = center_x, mouse_y = center_y;
	var angle_x = model.parameters.ids.indexOf("PARAM_ANGLE_X");
	var angle_y = model.parameters.ids.indexOf("PARAM_ANGLE_Y");
	var eye_x = model.parameters.ids.indexOf("PARAM_EYE_BALL_X");
	var eye_y = model.parameters.ids.indexOf("PARAM_EYE_BALL_Y");
	app.ticker.add(function (deltaTime) {
		//顔と目のパラメータをマウスカーソルの方向で上書き
		//※フレームワーク内では該当パラメータを更新しないよう修正している
		rect = canvas.getBoundingClientRect();
		center_x = pos_x + rect.left, center_y = pos_y + rect.top;
		var x = mouse_x - center_x;
		var y = mouse_y - center_y;
		model.parameters.values[angle_x] = x * 0.1;
		model.parameters.values[angle_y] = -y * 0.1;
		model.parameters.values[eye_x] = x * 0.005;
		model.parameters.values[eye_y] = -y * 0.005;

		//モーション更新
		model.update(deltaTime);
		model.masks.update(app.renderer);
	});
	documentElement = (navigator.userAgent.toLowerCase().match(/webkit/)) ? document.body : document.documentElement;
	document.body.addEventListener("mousemove", function(e){
		//マウスカーソル位置を更新
		mouse_x = e.pageX - documentElement.scrollLeft;
		mouse_y = e.pageY - documentElement.scrollTop;
	});
	document.body.addEventListener("click", function(e){
		//クリックされたらランダムでモーション再生
		if(motions.length == 0){ return; }
		if(rect.left < mouse_x && mouse_x < (rect.left + rect.width) && rect.top < mouse_y && mouse_y < (rect.top + rect.height)){
			var rand = Math.floor(Math.random() * motions.length);
			model.animator.getLayer("motion").stop();
			model.animator.getLayer("motion").play(motions[rand]);
		}
	});
	var onResize = function (event) {
		if (event === void 0) { event = null; }
		var width = window.innerWidth;
		var height = (width / 16.0) * 9.0;
		app.view.style.width = width + "px";
		app.view.style.height = height + "px";
		app.renderer.resize(width, height);

		//モデルの位置と大きさ
		model.position = new PIXI.Point(pos_x, pos_y);
		model.scale = new PIXI.Point(scale, scale);
		model.masks.resize(app.view.width, app.view.height);
	};
	onResize();
	window.onresize = onResize;
});
