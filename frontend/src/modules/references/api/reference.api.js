import http from '@/core/api/http';

export function getReferences(type) {
    return http.get('/references', {
        params: { type }
    });
}

export function createReference(data) {
    return http.post('/references', data);
}

export function updateReference(id, data) {
    return http.put(`/references/${id}`, data);
}

export function deleteReference(id) {
    return http.delete(`/references/${id}`);
}

export function deleteReferences(ids) {
    return http.delete('/references', {
        data: { ids }
    });
}