import axios from 'axios';

export const getTickets = async ( page, per_page, search, status, category ) => {
    try {
        return await axios.get('/api/tickets', { params: { page, per_page, search, status, category } }).then(handleResponse);
    } catch (error) {
        console.error('Error getting tickets:', error);
        throw error;
    }
}

export const getTicket = async (id) => {
    try {
        const response = await axios.get(`/api/tickets/${id}`).then(handleResponse);
        return response;
    } catch (error) {
        console.error('Error getting ticket:', error);
        throw error;
    }
}

export const createTicket = async (ticket) => {
   try {
    return await axios.post('/api/tickets', ticket).then(handleResponse);
   } catch (error) {
    console.error('Error creating ticket:', error);
    throw error;
   }
}

export const getDashboardStats = async () => {
    try {
        const response = await axios.get('/api/stats').then(handleResponse);
        return response;
    } catch (error) {
        console.error('Error getting dashboard stats:', error);
        throw error;
    }
}

export const classifyTicket = async (id) => {
    try {
        return await axios.post(`/api/tickets/${id}/classify`).then(handleResponse);
    } catch (error) {
        console.error('Error classifying ticket:', error);
        throw error;
    }
}

const handleResponse = (response) => {
    if (response.status === 200) {
        return response?.data;
    } else if (response.status === 422) {
        return Promise.reject(error);
    } else {
        return Promise.reject(error);
    }
}