import tw from 'twin.macro';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { object } from 'yup';
import { Form, Formik, useFormikContext, type FormikHelpers } from 'formik';

import Button from '@/components/elements/Button';
import createPort from '@/api/admin/ports/createPort';
import AdminContentBlock from '@/components/admin/AdminContentBlock';
import SpinnerOverlay from '@/components/elements/SpinnerOverlay';
import FlashMessageRender from '@/components/FlashMessageRender';
import useFlash from '@/plugins/useFlash';
import PortBaseSettings from './PortBaseSettings';

type FormState = any;

function InternalForm() {
    const { isSubmitting, isValid, setFieldValue, values } = useFormikContext<FormState>();

    return (
        <Form>
            <div css={tw`grid grid-cols-2 gap-y-6 gap-x-8 mb-16`}>
                
                <div css={tw`grid grid-cols-1 gap-y-6 col-span-2 md:col-span-1`}>
                    <PortBaseSettings/>
                </div>
                
                <div css={tw`bg-neutral-700 rounded shadow-md px-4 py-3 col-span-2`}>
                    <div css={tw`flex flex-row`}>
                        <Button type="submit" size="small" css={tw`ml-auto`} disabled={isSubmitting || !isValid}>
                            Create Port
                        </Button>
                    </div>
                </div>

            </div>
        </Form>
    );
}


export default function NewPortContainer(){
    const navigate = useNavigate();
    const { clearFlashes, clearAndAddHttpError, addFlash } = useFlash();

    const submit = (r: FormState, { setSubmitting }: FormikHelpers<FormState>) => {
        clearFlashes("port:create");

        createPort(r)
        .then(() => {
            addFlash({ key: "ports", message: "Port will be ready soon.", type: "success" });
            navigate("/admin/ports");
        })
        .catch(error=>clearAndAddHttpError({ key: "server:create", error }))
        .then(()=>setSubmitting(false));
    }

    return (
        <AdminContentBlock title="New Port">
            <div css={tw`w-full flex flex-row items-center mb-8`}>
                <div css={tw`flex flex-col flex-shrink`} style={{ minWidth: '0' }}>
                    <h2 css={tw`text-2xl text-neutral-50 font-header font-medium`}>New Port</h2>
                    <p css={tw`text-base text-neutral-400 whitespace-nowrap overflow-ellipsis overflow-hidden`}>
                        Add a new port to the panel.
                    </p>
                </div>
            </div>  

            <FlashMessageRender byKey={'port:create'} css={tw`mb-4`} />

            <Formik onSubmit={submit} initialValues={{}}>
                <InternalForm/>
            </Formik>

        </AdminContentBlock>
    );
}