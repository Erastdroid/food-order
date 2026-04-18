import React from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider, useAuth } from './context/AuthContext';
import { CartProvider } from './context/CartContext';
import Login from './components/Auth/Login';
import Register from './components/Auth/Register';
import RestaurantList from './components/Restaurants/RestaurantList';
import OrderList from './components/Orders/OrderList';
import Cart from './components/Cart/Cart';

function ProtectedRoute({ children }) {
    const { token } = useAuth();
    return token ? children : <Navigate to="/login" />;
}

function App() {
    return (
        <Router>
            <AuthProvider>
                <CartProvider>
                    <div className="App">
                        <Routes>
                            <Route path="/login" element={<Login />} />
                            <Route path="/register" element={<Register />} />
                            <Route path="/" element={<RestaurantList />} />
                            <Route
                                path="/orders"
                                element={
                                    <ProtectedRoute>
                                        <OrderList />
                                    </ProtectedRoute>
                                }
                            />
                            <Route
                                path="/cart"
                                element={
                                    <ProtectedRoute>
                                        <Cart />
                                    </ProtectedRoute>
                                }
                            />
                        </Routes>
                    </div>
                </CartProvider>
            </AuthProvider>
        </Router>
    );
}

export default App;
