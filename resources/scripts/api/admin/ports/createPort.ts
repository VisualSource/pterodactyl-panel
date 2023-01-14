import http from '@/api/http';
import { type Port, rawDataToPort } from '@/api/admin/ports/getPorts';

interface CreateProps {
    allocation_id: number;
    internal_port?: number | null;
    external_port: number;
    type: string;
    method: string;
    description?: string | null;
    internal_address?: string | null; 
}

export default (data: CreateProps, include: string[] = []): Promise<Port> => {
    return new Promise((ok,rej)=>{
        http.post("/api/application/ports",data,{
            params: {
                include: include.join(",")
            }
        })
        .then(({data})=>ok(rawDataToPort(data)))
        .catch(rej)
    });
} 