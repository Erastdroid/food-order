import React, { createContext, useState, useContext } from 'react';

const CartContext = createContext();

export function CartProvider({ children }) {
    const [cart, setCart] = useState([]);
    const [restaurantId, setRestaurantId] = useState(null);

    const addItem = (item) => {
        if (restaurantId && restaurantId !== item.restaurant_id) {
            alert('Please clear cart to order from different restaurant');
            return;
        }

        const existingItem = cart.find((i) => i.menu_item_id === item.menu_item_id);

        if (existingItem) {
            setCart(
                cart.map((i) =>
                    i.menu_item_id === item.menu_item_id
                        ? { ...i, quantity: i.quantity + (item.quantity || 1) }
                        : i
                )
            );
        } else {
            setCart([...cart, { ...item, quantity: item.quantity || 1 }]);
        }

        setRestaurantId(item.restaurant_id);
    };

    const removeItem = (menuItemId) => {
        setCart(cart.filter((i) => i.menu_item_id !== menuItemId));
    };

    const clearCart = () => {
        setCart([]);
        setRestaurantId(null);
    };

    const getTotal = () => {
        return cart.reduce((total, item) => total + item.price * item.quantity, 0);
    };

    return (
        <CartContext.Provider value={{ cart, addItem, removeItem, clearCart, getTotal, restaurantId }}>
            {children}
        </CartContext.Provider>
    );
}

export function useCart() {
    return useContext(CartContext);
}
