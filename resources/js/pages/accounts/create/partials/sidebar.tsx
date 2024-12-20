import * as TabsPrimitives from "@radix-ui/react-tabs";
import * as React from "react";
import ArrowLeftSIcon from "virtual:icons/ri/arrow-left-s-line";
import ArrowRightSIcon from "virtual:icons/ri/arrow-right-s-line";
import CloseIcon from "virtual:icons/ri/close-line";
import HeadphoneIcon from "virtual:icons/ri/headphone-line";

import * as Button from "#/components/ui/button.tsx";
import * as VerticalStepper from "#/components/ui/vertical-stepper.tsx";
import { type useStepper } from "./stepper.ts";

export function FlowSidebar({ stepper }: { stepper: ReturnType<typeof useStepper> }) {
    const getState = (index: number) => {
        if (stepper.current.index > index) return "completed";
        if (stepper.current.index === index) return "active";
        return "default";
    };

    return (
        <>
            {/* mobile */}
            <div className="border-b border-(--stroke-soft-200) lg:hidden">
                <div className="px-2.5 pt-2.5 pb-3.5">
                    <div className="relative flex h-9 items-center justify-between">
                        <div className="flex gap-2">
                            <Button.Root
                                $size="xs"
                                $style="stroke"
                                $type="neutral"
                                disabled={stepper.isFirst}
                                onClick={stepper.next}
                            >
                                <Button.Icon as={ArrowLeftSIcon} />
                                Prev
                            </Button.Root>
                            <Button.Root
                                $size="xs"
                                $style="stroke"
                                $type="neutral"
                                disabled={stepper.isLast}
                                onClick={stepper.prev}
                            >
                                <Button.Icon as={ArrowRightSIcon} />
                                Next
                            </Button.Root>
                        </div>
                        <Button.Root $style="ghost" $type="neutral">
                            <Button.Icon as={CloseIcon} />
                        </Button.Root>
                    </div>
                </div>

                <div className="h-1 w-full bg-(--bg-soft-200)">
                    <div
                        className="h-full bg-primary transition-all duration-200 ease-out"
                        style={{
                            width: `${(100 / stepper.all.length) * (stepper.current.index + 1)}%`,
                        }}
                    />
                </div>

                <div className="flex items-center justify-between p-4">
                    <div className="flex items-center gap-2">
                        <div className="flex size-5 items-center justify-center rounded-full bg-primary text-label-xs text-white">
                            {stepper.current.index + 1}
                        </div>
                        <span className="text-paragraph-sm text-(--text-strong-950)">
                            {stepper.all[stepper.current.index]?.label}
                        </span>
                    </div>
                    <div className="text-right text-paragraph-sm text-(--text-soft-400)">
                        {stepper.current.index + 1}/{stepper.all.length}
                    </div>
                </div>
            </div>

            {/* desktop */}
            <div className="hidden flex-1 flex-col self-stretch p-2 lg:flex">
                <div className="flex w-[264px] flex-1 shrink-0 flex-col gap-3 rounded-16 bg-(--bg-weak-50) px-4 pt-5 pb-4">
                    <div className="w-full flex-1">
                        <div className="mb-3 text-subheading-xs text-(--text-soft-400) uppercase">Create Account</div>

                        <TabsPrimitives.Root value={stepper.current.id}>
                            <VerticalStepper.Root asChild className="w-[232px] shrink-0">
                                <TabsPrimitives.List>
                                    {stepper.all.map((step, index) => (
                                        <React.Fragment key={step.id}>
                                            <VerticalStepper.Item $state={getState(index)} asChild>
                                                <TabsPrimitives.Trigger value={step.id}>
                                                    <VerticalStepper.ItemIndicator>
                                                        {index + 1}
                                                    </VerticalStepper.ItemIndicator>
                                                    {step.label}
                                                    {stepper.current.id !== step.id ? <VerticalStepper.Arrow /> : null}
                                                </TabsPrimitives.Trigger>
                                            </VerticalStepper.Item>
                                        </React.Fragment>
                                    ))}
                                </TabsPrimitives.List>
                            </VerticalStepper.Root>
                        </TabsPrimitives.Root>
                    </div>

                    <div className="flex flex-col gap-4 text-center">
                        <div className="text-paragraph-sm text-(--text-sub-600)">Having trouble with transfer?</div>
                        <Button.Root $style="stroke" $type="neutral">
                            <Button.Icon as={HeadphoneIcon} />
                            Contact
                        </Button.Root>
                    </div>

                    <div className="text-center text-paragraph-xs text-(--text-soft-400)">
                        © {new Date().getFullYear()} ExpenseTrackr
                    </div>
                </div>
            </div>
        </>
    );
}
