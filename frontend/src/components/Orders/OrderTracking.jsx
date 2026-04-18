import React, { useEffect, useState, useRef } from 'react';
import { useAuth } from '../../context/AuthContext';

export default function OrderTracking({ orderId }) {
    const [order, setOrder] = useState(null);
    const [deliveryLocation, setDeliveryLocation] = useState(null);
    const { token } = useAuth();
    const mapRef = useRef(null);
    const echoRef = useRef(null);

    useEffect(() => {
        fetchOrderDetails();
        initializeRealtime();

        return () => {
            if (echoRef.current) {
                echoRef.current.leaveChannel(`order.${orderId}`);
            }
        };
    }, [orderId]);

    const fetchOrderDetails = async () => {
        try {
            const response = await fetch(
                `http://localhost:8000/api/orders/${orderId}/track`,
                {
                    headers: { Authorization: `Bearer ${token}` },
                }
            );
            const data = await response.json();
            setOrder(data);
        } catch (error) {
            console.error('Failed to fetch order:', error);
        }
    };

    const initializeRealtime = async () => {
        // Using Reverb (Laravel WebSockets)
        const Echo = (await import('laravel-echo')).default;
        const Pusher = (await import('pusher-js')).default;

        window.Pusher = Pusher;

        const echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST,
            wsPort: import.meta.env.VITE_REVERB_PORT,
            wssPort: import.meta.env.VITE_REVERB_PORT,
            forceTLS: false,
            encrypted: true,
        });

        echoRef.current = echo;

        echo.private(`order.${orderId}`)
            .listen('order.status.changed', (event) => {
                setOrder((prev) => ({
                    ...prev,
                    status: event.status,
                }));
            })
            .listen('delivery.location.updated', (event) => {
                setDeliveryLocation({
                    latitude: event.latitude,
                    longitude: event.longitude,
                });
                updateMapMarker(event.latitude, event.longitude);
            });
    };

    const updateMapMarker = (lat, lng) => {
        // Update map with new delivery person location
        console.log(`Delivery person location updated: ${lat}, ${lng}`);
    };

    const getStatusIcon = (status) => {
        const icons = {
            pending: '⏳',
            confirmed: '✅',
            preparing: '👨‍🍳',
            ready: '📦',
            on_the_way: '🚗',
            delivered: '✨',
            cancelled: '❌',
        };
        return icons[status] || '📍';
    };

    if (!order) return <div>Loading...</div>;

    return (
        <div className="order-tracking-container">
            <h2>Track Your Order #{order.order_id}</h2>

            <div className="status-timeline">
                <div className={`status-step ${order.status === 'confirmed' || 'pending' ? 'active' : ''}`}>
                    <span>✅ Confirmed</span>
                </div>
                <div className={`status-step ${order.status === 'preparing' ? 'active' : ''}`}>
                    <span>👨‍🍳 Preparing</span>
                </div>
                <div className={`status-step ${order.status === 'ready' ? 'active' : ''}`}>
                    <span>📦 Ready</span>
                </div>
                <div className={`status-step ${order.status === 'on_the_way' ? 'active' : ''}`}>
                    <span>🚗 On the Way</span>
                </div>
                <div className={`status-step ${order.status === 'delivered' ? 'active' : ''}`}>
                    <span>✨ Delivered</span>
                </div>
            </div>

            <div className="map-container" ref={mapRef}>
                <div className="status-badge">
                    {getStatusIcon(order.status)} {order.status.toUpperCase()}
                </div>
                {deliveryLocation && (
                    <div className="delivery-info">
                        <p>📍 Driver Location: {deliveryLocation.latitude}, {deliveryLocation.longitude}</p>
                    </div>
                )}
            </div>

            <div className="order-details">
                <h3>{order.restaurant_address.address}</h3>
                <p>⏱️ Estimated Delivery: {order.estimated_delivery_time} minutes</p>
                <p>💰 Total: ${order.total_amount}</p>
            </div>
        </div>
    );
}
