{
    const autoLoadDelay = wputilAutoLoadDelay;
	const eventList = ["mouseover","keydown","touchmove","touchstart"];
    const load = () => {
		const event = new Event("DOMUserInteraction");
		document.dispatchEvent(event);

		console.log("interacted");

		document.querySelectorAll("script[data-type=lazy]").forEach(el => el.src = el.dataset.src);

		eventList.forEach(e => window.removeEventListener(e, trigger, {passive: true, once: true}));
	}
    const timer = setTimeout(load, autoloadDelay);
    const trigger = () => {
        load();
        clearTimeout(timer);
    };
    eventList.forEach(e => window.addEventListener(e, trigger, {passive: true, once: true}));
}