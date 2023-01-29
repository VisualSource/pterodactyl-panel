import { faCogs } from '@fortawesome/free-solid-svg-icons';
import { useFormikContext } from 'formik';
import tw from 'twin.macro';
import getNodes from '@/api/admin/nodes/getNodes';
import { getAllocations } from '@/api/admin/node';
import { portTypes } from '@/api/admin/ports/getPorts';

import AdminBox from '@/components/admin/AdminBox';
import Field from '@/components/elements/Field';
import Label from '@/components/elements/Label';
import Select from '@/components/elements/Select';
import { useState } from 'react';
import useSWR from 'swr';

export default () => {
    const { isSubmitting, setFieldValue, initialValues } = useFormikContext<any>();
    const [selectedNode, setSelectedNode] = useState<number>();
    const nodes = getNodes();
    const allocations = useSWR([selectedNode, 'ports-new-allocations'], async (args: [number | undefined, ...any]) => {
        if (args[0]) return getAllocations(args[0]);
        return [];
    });

    return (
        <AdminBox icon={faCogs} title="Settings" isLoading={isSubmitting}>
            <div css={tw`grid grid-cols-1 xl:grid-cols-2 gap-4 lg:gap-6`}>
                <Field
                    defaultValue={initialValues?.external_port}
                    id="external_port"
                    name="external_port"
                    label="External Port"
                    type="number"
                />
                <div>
                    <Label htmlFor="type">Network Type</Label>
                    <Select
                        id="type"
                        name="type"
                        defaultValue={initialValues?.type ?? 'both'}
                        onChange={ev => setFieldValue('type', ev.target.value)}
                    >
                        {Object.entries(portTypes).map(([key, value]) => (
                            <option key={value} value={value}>
                                {key}
                            </option>
                        ))}
                    </Select>
                </div>
                {!nodes.error || !nodes.isLoading ? (
                    <div>
                        <Label htmlFor="node">Node</Label>
                        <Select id="node" onChange={ev => setSelectedNode(parseInt(ev.target.value) ?? undefined)}>
                            <option>Select Node</option>
                            {nodes.data?.items.map((value, key) => (
                                <option key={key} value={value.id}>
                                    {value.name}
                                </option>
                            ))}
                        </Select>
                    </div>
                ) : null}

                <div>
                    <Label htmlFor="allocation_id">Allocation</Label>
                    <Select
                        defaultValue={initialValues?.allocation_id}
                        id="allocation_id"
                        name="allocation_id"
                        onChange={ev => setFieldValue('allocation_id', parseInt(ev.target.value))}
                    >
                        {!allocations.error || !allocations.isLoading ? (
                            <>
                                <option>Select Allocaiton</option>
                                {allocations.data?.map((value, key) => (
                                    <option key={key} value={value.id}>
                                        {value.getDisplayText()}
                                    </option>
                                ))}
                            </>
                        ) : (
                            <option>Loading...</option>
                        )}
                    </Select>
                </div>
            </div>
        </AdminBox>
    );
};
