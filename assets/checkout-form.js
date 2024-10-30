(function () {
    let BLPACZKA_el = window.React;
    let BLPACZKA_data = window.wp.data;
    let BLPACZKA_blocks = window.wp.blocks;
    const { ExperimentalOrderMeta: BLPACZKA_OrderMeta } = wc.blocksCheckout;
    const { subscribe, select } = wp.data;
    let BLPACZKA_inputParams = {
        "name": "extended-checkout/blpaczka-point-selector",
        "parent": ["woocommerce/checkout-contact-information-block"],
        "attributes": {
            "lock": {"type": "object", "default": {"remove": true, "move": true}},
            "text": {
                "type": "string",
                "source": "html",
                "selector": ".wp-block-woocommerce-checkout-newsletter-subscription",
                "default": ""
            }
        }
    };

    function BLPACZKA_renderPointSelector(props) {
        let {handleLockerChange: handleChange, blpaczkaPoint: point, nonce} = props;
        const [selectedPoint, setSelectedPoint] = (0, BLPACZKA_el.useState)(null);
        return (0, BLPACZKA_el.useEffect)((() => {
            const selected = point;
            setSelectedPoint(selected);
        }), [point]), (0, BLPACZKA_el.createElement)("div", {className: "blpaczka-delivery-point-wrap"},
            (0, BLPACZKA_el.createElement)("input", {
                id: "blpaczka-point",
                type: "text",
                value: point,
                hidden: "hidden",
                onChange: e => {
                    handleChange(e);
                }
            }),
            (0, BLPACZKA_el.createElement)("input", {
                type: "hidden",
                name: "blpaczka_pickup_nonce",
                value: blpaczkaData.nonce
            })
        );
    }
    window.wp.blockEditor;
    const {registerCheckoutBlock: registerBlock} = wc.blocksCheckout, {name: blockName} = BLPACZKA_inputParams;
    !!(0, BLPACZKA_data.select)("core/editor") && (0, BLPACZKA_blocks.registerBlockType)(blockName, {}),
        registerBlock({
            metadata: BLPACZKA_inputParams, component: props => {
                let {checkoutExtensionData: extData} = props;
                const [selectedPoint, setSelectedPoint] = (0, BLPACZKA_el.useState)(null), [pointInput, setPointInput] = (0, BLPACZKA_el.useState)(""), {setExtensionData: setExtData} = extData;
                return (0, BLPACZKA_el.useEffect)((() => {
                    setExtData("blpaczka", "blpaczka-point", pointInput);
                }), [setExtData, pointInput]), (0, BLPACZKA_el.createElement)(BLPACZKA_el.Fragment, null, (0, BLPACZKA_el.createElement)(BLPACZKA_OrderMeta, null, (0, BLPACZKA_el.createElement)(BLPACZKA_renderPointSelector, {
                    blpaczkaPoint: pointInput,
                    handleLockerChange: e => {
                        const value = e.target.value;
                        setSelectedPoint(e.target.value), setPointInput(e.target.value);
                    }
                })));
            }
        });
})();

